<?php

namespace App\Http\Controllers\Api;

use App\Events\JobCompletedEvent;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobReport;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobReportController extends Controller
{
    /**
     * الصنايعي يبعت التقرير (العيب، الحل، التكلفة، صور قبل وبعد اختياري)
     * لازم الطلب يكون معلق OTP (completed) قبل ما التقرير يتبعت
     */
    public function store(Request $request, Job $job)
    {
        if ($job->craftsman_id !== $request->user()->craftsmanProfile?->id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $validator = Validator::make($request->all(), [
            'defect_description' => 'required|string|max:2000',
            'work_done_description' => 'required|string|max:2000',
            'cost_breakdown' => 'required|array',
            'cost_breakdown.*.item' => 'required|string',
            'cost_breakdown.*.amount' => 'required|numeric',
            'before_photos' => 'nullable|array',
            'after_photos' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = JobReport::updateOrCreate(
            ['job_id' => $job->id],
            [
                'defect_description' => $request->defect_description,
                'work_done_description' => $request->work_done_description,
                'cost_breakdown_json' => $request->cost_breakdown,
                'before_photos' => $request->before_photos,
                'after_photos' => $request->after_photos,
            ]
        );

        // TODO: إشعار للعميل إن التقرير جاهز ومحتاج تأكيد (client_ack)

        return response()->json([
            'message' => 'تم إرسال التقرير، في انتظار تأكيد العميل',
            'report' => $report,
        ]);
    }

    /**
     * العميل يأكد إن الشغل تم بنجاح
     * ده اللي بيقفل الحلقة فعليًا: بيفعّل الريسيت + بيبلغ إسراء (verification_tier)
     * + بيبلغ بلال (العمولة) عن طريق الـ Event
     */
    public function clientAck(Request $request, Job $job)
    {
        if ($job->client_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $report = $job->report;

        if (! $report) {
            return response()->json(['message' => 'التقرير لسه متبعتش'], 422);
        }

        $report->update(['client_ack_at' => now()]);

        $receipt = $this->generateReceipt($job);

        $job->logStatusEvent('client_acknowledged');

        // نفس الحدث اللي إسراء بتسمعه عشان تحدّث verification_tier
        // وبلال هيسمعه كمان عشان يخصم العمولة (يعمل Listener مماثل عنده)
        event(new JobCompletedEvent($job->craftsman));

        return response()->json([
            'message' => 'تم تأكيد الشغل، شكرًا لتقييمك القادم!',
            'receipt' => $receipt,
        ]);
    }

    /**
     * توليد الريسيت (بدل العقد الرسمي) مع QR payload
     * لو الطلب B2B/متعدد الأيام هيتحول لـ formal_contract بدل receipt عادي
     * (التفرقة بينهم بتتحدد حسب نوع العميل/الطلب — نقطة لازم تتأكد مع الفريق)
     */
    private function generateReceipt(Job $job): Receipt
    {
        $qrPayload = Str::uuid()->toString(); // TODO: استبدليها بترميز فعلي لبيانات المعاملة

        return Receipt::create([
            'job_id' => $job->id,
            'type' => 'receipt', // TODO: منطق تحديد B2B لاحقًا يخليها formal_contract
            'qr_payload' => $qrPayload,
            // 'pdf_path' => ... // TODO: توليد PDF فعلي لو محتاجين نسخة قابلة للطباعة
        ]);
    }
}
