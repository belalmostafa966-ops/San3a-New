<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // عرض بيانات المحفظة الكاملة
    public function show(Request $request)
    {
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة لهذا المستخدم'], 404);
        }

        return response()->json([
            'balance' => $wallet->balance,
            'held_amount' => $wallet->held_amount,
            'available_balance' => $wallet->availableBalance(),
            'is_active' => $wallet->is_active,
        ]);
    }

    // تجميد مبلغ (Hold)
    public function hold(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'job_id' => 'nullable|integer',
        ]);

        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة لهذا المستخدم'], 404);
        }

        try {
            $wallet->holdAmount(
                $request->amount,
                $request->description,
                $request->reference_id,
                $request->job_id
            );
            return response()->json(['message' => 'تم تجميد المبلغ بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // تأكيد الخصم (Confirm)
    public function confirm(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'job_id' => 'nullable|integer',
        ]);

        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة لهذا المستخدم'], 404);
        }

        try {
            $wallet->confirmHold(
                $request->amount,
                $request->description,
                $request->reference_id,
                $request->job_id
            );
            return response()->json(['message' => 'تم تأكيد الخصم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // فك التجميد (Release)
    public function release(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'job_id' => 'nullable|integer',
        ]);

        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة لهذا المستخدم'], 404);
        }

        try {
            $wallet->releaseHold(
                $request->amount,
                $request->description,
                $request->reference_id,
                $request->job_id
            );
            return response()->json(['message' => 'تم فك التجميد بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // عرض سجل الحركات
    public function transactions(Request $request)
    {
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json(['message' => 'لا توجد محفظة لهذا المستخدم'], 404);
        }

        $transactions = $wallet->transactions()->latest()->paginate(20);

        return response()->json($transactions);
    }
}