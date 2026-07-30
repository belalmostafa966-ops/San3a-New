<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * الخطوة 1: طلب OTP للتسجيل أو الدخول
     * ملحوظة: في مرحلة التطوير بنرجع الكود نفسه في الـ response (debug_code)
     * عشان تختبري من غير SMS gateway حقيقي. لازم يتشال قبل الإنتاج.
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:8|max:20',
            'purpose' => 'required|in:register,login',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $code = (string) random_int(1000, 9999);

        OtpCode::create([
            'phone' => $request->phone,
            'code' => $code,
            'purpose' => $request->purpose,
            'expires_at' => now()->addMinutes(5),
        ]);

        // TODO: استبدلي دي بنداء فعلي على SMS gateway (فودافون / Twilio)
        $responseData = [
            'message' => 'تم إرسال كود التحقق',
        ];

        if (config('app.debug') || app()->environment('local', 'testing')) {
            $responseData['debug_code'] = $code;
        }

        return response()->json($responseData);

    }

    /**
     * الخطوة 2: تأكيد الـ OTP وإنشاء/تسجيل دخول المستخدم
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string',
            'purpose' => 'required|in:register,login',
            'role' => 'required_if:purpose,register|in:client,craftsman',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = OtpCode::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('purpose', $request->purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otp) {
            return response()->json(['message' => 'كود التحقق غير صحيح'], 422);
        }

        if ($otp->isExpired()) {
            return response()->json(['message' => 'انتهت صلاحية الكود، اطلبي كود جديد'], 422);
        }

        $otp->update(['verified_at' => now()]);

        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => 'مستخدم جديد',
                'password' => Hash::make(str()->random(16)),
                'role' => $request->role ?? 'client',
            ]
        );

        if ($user->isSuspended()) {
            return response()->json(['message' => 'حسابك موقوف، تواصلي مع الدعم'], 403);
        }

        $token = $user->createToken('sanaa-app')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
