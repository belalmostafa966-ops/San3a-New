<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    // عرض كل طلبات السحب بتاعة الحرفي
    public function index(Request $request)
    {
        $payouts = Payout::where('craftsman_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($payouts);
    }

    // طلب سحب جديد
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:fawry,instapay,vodafone_cash,bank_transfer',
        ]);

        $wallet = $request->user()->wallet;

        if ($wallet->availableBalance() < $request->amount) {
            return response()->json(['message' => 'الرصيد المتاح غير كافٍ لطلب السحب'], 400);
        }

        $wallet->holdAmount($request->amount, 'طلب سحب رصيد', null, null);

        $payout = Payout::create([
            'craftsman_id' => $request->user()->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        return response()->json([
            'message' => 'تم إرسال طلب السحب بنجاح، في انتظار المعالجة',
            'payout' => $payout,
        ]);
    }
}