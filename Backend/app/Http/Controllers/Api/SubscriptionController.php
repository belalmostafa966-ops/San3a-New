<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // عرض كل الباقات المتاحة
    public function plans()
    {
        return response()->json(SubscriptionPlan::all());
    }

    // عرض اشتراك الحرفي الحالي
    public function current(Request $request)
    {
        $subscription = Subscription::where('craftsman_id', $request->user()->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'لا يوجد اشتراك فعّال'], 404);
        }

        return response()->json($subscription->load('plan'));
    }

    // الاشتراك في باقة جديدة
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة مرتبطة بهذا الحساب'], 400);
        }

        // التأكد إن الرصيد المتاح كافي
        if ($wallet->availableBalance() < $plan->price) {
            return response()->json(['message' => 'الرصيد غير كافٍ للاشتراك في هذه الباقة'], 400);
        }

        try {
            // تنفيذ الخصم والاشتراك في Transaction واحدة لضمان سلامة البيانات
            $subscription = DB::transaction(function () use ($request, $plan, $wallet) {
                // إلغاء أي اشتراك سابق فعّال
                Subscription::where('craftsman_id', $request->user()->id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);

                // خصم قيمة الاشتراك من المحفظة
                $wallet->balance -= $plan->price;
                $wallet->save();

                // تسجيل حركة السحب
                $wallet->transactions()->create([
                    'type' => 'withdrawal',
                    'amount' => $plan->price,
                    'balance_after' => $wallet->balance,
                    'description' => 'اشتراك في باقة ' . $plan->name,
                ]);

                // إنشاء الاشتراك الجديد
                return Subscription::create([
                    'craftsman_id' => $request->user()->id,
                    'plan_id' => $plan->id,
                    'starts_at' => Carbon::now(),
                    'ends_at' => Carbon::now()->addMonth(),
                    'status' => 'active',
                ]);
            });

            return response()->json([
                'message' => 'تم الاشتراك بنجاح',
                'subscription' => $subscription->load('plan'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الاشتراك: ' . $e->getMessage()], 500);
        }
    }

    // إلغاء الاشتراك الحالي
    public function cancel(Request $request)
    {
        $subscription = Subscription::where('craftsman_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'لا يوجد اشتراك فعّال لإلغائه'], 404);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'تم إلغاء الاشتراك بنجاح']);
    }
}