<?php

namespace App\Http\Controllers\Api;

use App\Events\JobCompletedEvent;
use App\Http\Controllers\Controller;
use App\Models\GpsPing;
use App\Models\Job;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * الصنايعي يدوس "أنا في الطريق" -> يبدأ تتبع الـ GPS
     */
    public function startOnTheWay(Request $request, Job $job)
    {
        $this->authorizeCraftsman($request, $job);

        $job->update(['status' => 'on_the_way']);
        $job->logStatusEvent('on_the_way');

        return response()->json(['message' => 'تم تسجيل بدء التحرك', 'job' => $job]);
    }

    /**
     * تسجيل نقطة GPS (تتبعت من الأبلكيشن كل فترة والصنايعي في الطريق/عند العميل)
     */
    public function pingLocation(Request $request, Job $job)
    {
        $this->authorizeCraftsman($request, $job);

        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        GpsPing::create([
            'job_id' => $job->id,
            'craftsman_id' => $job->craftsman_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'recorded_at' => now(),
        ]);

        // أول ping بيعتبر إن الصنايعي وصل -> بدء in_progress لو لسه مبدأش
        if ($job->status === 'on_the_way') {
            $job->update(['status' => 'in_progress', 'started_at' => now()]);
            $job->logStatusEvent('arrived_started');
        }

        return response()->json(['message' => 'تم تسجيل الموقع']);
    }

    /**
     * الصنايعي يطلب قفل الطلب -> إرسال OTP للعميل
     */
    public function requestCloseOtp(Request $request, Job $job)
    {
        $this->authorizeCraftsman($request, $job);

        $code = (string) random_int(1000, 9999);

        OtpCode::create([
            'phone' => $job->client->phone,
            'code' => $code,
            'purpose' => 'close_job',
            'expires_at' => now()->addMinutes(10),
        ]);

        // TODO: ابعتي الكود فعليًا عن طريق SMS gateway أو push notification للعميل

        $responseData = [
            'message' => 'تم إرسال كود الإغلاق للعميل',
        ];

        if (config('app.debug') || app()->environment('local', 'testing')) {
            $responseData['debug_code'] = $code;
        }

        return response()->json($responseData);

    }

    /**
     * العميل يدخل الـ OTP -> الطلب يتقفل رسميًا
     */
    public function confirmCloseOtp(Request $request, Job $job)
    {
        if ($job->client_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = OtpCode::where('phone', $job->client->phone)
            ->where('code', $request->code)
            ->where('purpose', 'close_job')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired()) {
            return response()->json(['message' => 'كود غير صحيح أو منتهي'], 422);
        }

        $otp->update(['verified_at' => now()]);

        $job->update([
            'status' => 'completed',
            'otp_code' => $request->code,
            'otp_confirmed_at' => now(),
            'completed_at' => now(),
        ]);

        $job->jobRequest->update(['status' => 'completed']);
        $job->logStatusEvent('otp_confirmed');

        return response()->json(['message' => 'تم إغلاق الطلب بنجاح', 'job' => $job]);
    }

    /**
     * إلغاء الطلب — هنا فخ الإلغاء (Cancellation Trap)
     * لو الصنايعي بيلغي وهو already عند موقع العميل (فيه GPS pings مسجلة)
     * نبعت سؤال تأكيد للعميل بدل ما نقبل الإلغاء على طول
     */
    public function cancel(Request $request, Job $job)
    {
        $this->authorizeCraftsman($request, $job);

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $wasAtClientLocation = $job->gpsPings()->exists();

        if ($wasAtClientLocation && $job->status === 'in_progress') {
            // منطبقة عليه فخ الإلغاء: مش هيتلغى مباشرة، هيتحط pending_client_confirmation
            $job->logStatusEvent('cancellation_attempted_at_location', [
                'reason' => $request->reason,
            ]);

            // TODO: ابعتي إشعار فوري للعميل: "هل الصنايعي حاول ينفذ الطلب فعلاً؟"
            // ورد العميل هيوصل على endpoint تاني (confirmCancellation) يقرر
            // هل ده إلغاء حقيقي (strike يتسجل عند زياد) أو مجرد إلغاء عادي

            return response()->json([
                'message' => 'الطلب اتسجل كمعلق لحد ما العميل يأكد الموقف، عشان الصنايعي كان في الموقع',
                'requires_client_confirmation' => true,
            ]);
        }

        // إلغاء عادي (لسه ملوش وصول للموقع)
        $job->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->reason,
            'cancelled_by' => 'craftsman',
        ]);

        $job->jobRequest->update(['status' => 'open']); // يترجع يقبل proposals تانية
        $job->logStatusEvent('cancelled', ['reason' => $request->reason]);

        return response()->json(['message' => 'تم إلغاء الطلب']);
    }

    /**
     * رد العميل على سؤال فخ الإلغاء
     * confirmed = true يعني الصنايعي فعلاً كان بيهرب من العمولة
     */
    public function confirmCancellationTrap(Request $request, Job $job)
    {
        if ($job->client_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $validator = Validator::make($request->all(), [
            'craftsman_attempted_work' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => $request->craftsman_attempted_work
                ? 'client_confirmed_craftsman_attempted'
                : 'client_confirmed_craftsman_evaded',
            'cancelled_by' => 'craftsman',
        ]);

        $job->logStatusEvent('cancellation_trap_resolved', [
            'craftsman_attempted_work' => $request->craftsman_attempted_work,
        ]);

        if (! $request->craftsman_attempted_work) {
            // TODO: هنا تنادي حدث/سيرفس عند زياد يسجل strike ويخصم متوسط العمولة
            // من محفظة الصنايعي كعقوبة، حسب منطق الـ PDF بالظبط
            // event(new \App\Events\CraftsmanEvasionDetected($job));
        }

        return response()->json(['message' => 'تم تسجيل رد العميل']);
    }

    private function authorizeCraftsman(Request $request, Job $job): void
    {
        $craftsmanProfile = $request->user()->craftsmanProfile;

        abort_unless(
            $craftsmanProfile && $job->craftsman_id === $craftsmanProfile->id,
            403,
            'غير مصرح لك'
        );
    }
}
