<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\CommissionRule;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // عرض كل مدفوعات المستخدم الحالي
    public function index(Request $request)
    {
        $payments = Payment::where('payer_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($payments);
    }

    // تسجيل عملية دفع جديدة (أونلاين أو كاش)
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,fawry,instapay,vodafone_cash,wallet',
        ]);

        $payment = Payment::create([
            'job_id' => $request->job_id,
            'payer_id' => $request->user()->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'status' => 'pending',
        ]);

        // لو الدفع من المحفظة، ننفذ الخصم على طول
        if ($request->method === 'wallet') {
            $wallet = $request->user()->wallet;

            if ($wallet->availableBalance() < $request->amount) {
                $payment->update(['status' => 'failed']);
                return response()->json(['message' => 'الرصيد غير كافٍ'], 400);
            }

            $wallet->balance -= $request->amount;
            $wallet->save();

            $wallet->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance,
                'job_id' => $request->job_id,
                'description' => 'دفع عبر المحفظة',
            ]);

            $payment->update(['status' => 'completed', 'paid_at' => now()]);
        }

        return response()->json([
            'message' => 'تم تسجيل عملية الدفع',
            'payment' => $payment,
        ]);
    }

    // معالجة عملية دفع كاش (بعد ما الحرفي ياخد الفلوس كاش من العميل)
    // بيسجل نسبة المنصة كمديونية على محفظة الحرفي (زي ما موضح في الـ PDF)
    public function confirmCashPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'craftsman_id' => 'required|exists:users,id',
            'profession_id' => 'nullable|integer',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        // TODO: نسبة العمولة هنا بتاخد أول قاعدة عامة موجودة (profession_id = null)
        // لو فيه قاعدة خاصة بالمهنة، تتاخد بالأولوية - محتاج مراجعة الـ logic ده مع الفريق
        $rule = CommissionRule::where('profession_id', $request->profession_id)
            ->first() ?? CommissionRule::whereNull('profession_id')->first();

        $commissionPercent = $rule ? $rule->min_percent : 10; // fallback احتياطي لو مفيش قاعدة أصلاً
        $commissionAmount = ($payment->amount * $commissionPercent) / 100;
        
$craftsmanWallet = \App\Models\Wallet::where('user_id', $request->craftsman_id)->first();

if ($craftsmanWallet->hasReachedCreditLimit()) {
    return response()->json([
        'message' => 'الصنايعي وصل لحد المديونية المسموح، لا يمكنه استلام شغل جديد حتى يسدد المديونية',
    ], 403);
}


        // تسجيل العمولة كمديونية (رصيد بالسالب) على محفظة الحرفي
        $craftsmanWallet->balance -= $commissionAmount;
        $craftsmanWallet->save();

        $craftsmanWallet->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $commissionAmount,
            'balance_after' => $craftsmanWallet->balance,
            'job_id' => $payment->job_id,
            'description' => "عمولة المنصة ({$commissionPercent}%) على دفعة كاش",
        ]);

        $payment->update(['status' => 'completed', 'paid_at' => now()]);

        return response()->json([
            'message' => 'تم تسجيل العمولة كمديونية على الصنايعي',
            'commission_amount' => $commissionAmount,
            'craftsman_new_balance' => $craftsmanWallet->balance,
        ]);
    }
}