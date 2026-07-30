<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutAdminController extends Controller
{
    // عرض كل طلبات السحب المعلقة
    public function pending()
    {
        $payouts = Payout::where('status', 'requested')
            ->with('craftsman:id,name,phone')
            ->latest()
            ->paginate(20);

        return response()->json($payouts);
    }

    // الموافقة على طلب السحب (بعد ما الأدمن ينفذ التحويل فعلياً بره النظام)
    public function approve(Payout $payout)
    {
        if ($payout->status !== 'requested') {
            return response()->json(['message' => 'الطلب ده اتعالج بالفعل'], 400);
        }

        $wallet = $payout->craftsman->wallet;

        // تأكيد الخصم الفعلي (كان متجمد، دلوقتي بيتخصم بشكل نهائي)
        $wallet->confirmHold($payout->amount, 'تنفيذ طلب سحب', 'payout_' . $payout->id);

        $payout->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return response()->json(['message' => 'تم تنفيذ عملية السحب بنجاح']);
    }

    // رفض طلب السحب (بيرجع المبلغ المجمد للرصيد المتاح)
    public function reject(Request $request, Payout $payout)
    {
        if ($payout->status !== 'requested') {
            return response()->json(['message' => 'الطلب ده اتعالج بالفعل'], 400);
        }

        $wallet = $payout->craftsman->wallet;

        $wallet->releaseHold($payout->amount, 'رفض طلب سحب', 'payout_' . $payout->id);

        $payout->update([
            'status' => 'rejected',
            'processed_at' => now(),
        ]);

        return response()->json(['message' => 'تم رفض طلب السحب وإرجاع المبلغ للرصيد']);
    }
}