<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobProposal;
use App\Models\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobProposalController extends Controller
{
    /**
     * الصنايعي يبعت عرض سعر على طلب
     */
    public function store(Request $request, JobRequest $jobRequest)
    {
        $validator = Validator::make($request->all(), [
            'price_quote' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($jobRequest->status !== 'open') {
            return response()->json(['message' => 'الطلب ده مقفول أو اتاخد بالفعل'], 422);
        }

        $craftsmanProfile = $request->user()->craftsmanProfile;

        if (! $craftsmanProfile) {
            return response()->json(['message' => 'الحساب ده مش صنايعي'], 403);
        }

        $proposal = JobProposal::updateOrCreate(
            [
                'job_request_id' => $jobRequest->id,
                'craftsman_id' => $craftsmanProfile->id,
            ],
            [
                'price_quote' => $request->price_quote,
                'message' => $request->message,
                'status' => 'pending',
            ]
        );

        // TODO: إشعار للعميل بوصول عرض جديد

        return response()->json([
            'message' => 'تم إرسال العرض',
            'proposal' => $proposal,
        ], 201);
    }

    /**
     * العميل يقبل عرض معين -> بيتحول لـ Job فعلي
     * وباقي العروض بترفض تلقائيًا
     */
    public function accept(Request $request, JobProposal $proposal)
    {
        $jobRequest = $proposal->jobRequest;

        if ($jobRequest->client_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        if ($jobRequest->status !== 'open') {
            return response()->json(['message' => 'الطلب ده اتقفل بالفعل'], 422);
        }

        if (! $jobRequest->isVisitFeePaid()) {
            return response()->json(['message' => 'لازم تدفعي مصاريف المعاينة الأول'], 422);
            // ملحوظة: التحقق ده بيتم على حالة visit_fee_status اللي بيحدثها بلال
            // بعد الدفع الفعلي
        }

        $job = DB::transaction(function () use ($proposal, $jobRequest) {
            $proposal->update(['status' => 'accepted']);

            $jobRequest->proposals()
                ->where('id', '!=', $proposal->id)
                ->update(['status' => 'rejected']);

            $jobRequest->update(['status' => 'proposal_accepted']);

            $job = Job::create([
                'job_request_id' => $jobRequest->id,
                'craftsman_id' => $proposal->craftsman_id,
                'client_id' => $jobRequest->client_id,
                'status' => 'accepted',
            ]);

            $job->logStatusEvent('proposal_accepted', ['proposal_id' => $proposal->id]);

            return $job;
        });

        // TODO: إشعار للصنايعي إن العرض اتقبل

        return response()->json([
            'message' => 'تم قبول العرض وبدء الطلب',
            'job' => $job,
        ]);
    }
}
