<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanProfile;
use App\Models\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobRequestController extends Controller
{
    /**
     * الخطوة 1: العميل ينشر المشكلة
     * الوصف إجباري حسب الـ PDF
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profession_id' => 'required|exists:professions,id',
            'description' => 'required|string|min:10|max:2000',
            'zone_id' => 'required|exists:zones,id',
            'address' => 'required|string|max:500',
            'preferred_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobRequest = JobRequest::create([
            'client_id' => $request->user()->id,
            'profession_id' => $request->profession_id,
            'description' => $request->description,
            'zone_id' => $request->zone_id,
            'address' => $request->address,
            'preferred_time' => $request->preferred_time,
            'status' => 'open',
        ]);

        $this->notifyMatchingCraftsmen($jobRequest);

        return response()->json([
            'message' => 'تم نشر الطلب، وجاري إشعار الصنايعية المناسبين',
            'job_request' => $jobRequest,
        ], 201);
    }

    /**
     * الـ Routing: دور على الصنايعية المناسبين للطلب
     * (نفس المهنة + نفس المنطقة + معتمدين وسلوكهم كويس)
     */
    private function notifyMatchingCraftsmen(JobRequest $jobRequest): void
    {
        $matchingCraftsmen = CraftsmanProfile::query()
            ->where('profession_id', $jobRequest->profession_id)
            ->whereHas('zones', function ($query) use ($jobRequest) {
                $query->where('zones.id', $jobRequest->zone_id);
            })
            ->where('behavior_score', '>=', 5) // نفس شرط إسراء لمنع دخول الحسابات المنخفضة
            ->get();

        foreach ($matchingCraftsmen as $craftsman) {
            // TODO: هنا تنادي notification service حقيقي (push/SMS)
            // Notification::send($craftsman->user, new NewJobOpportunity($jobRequest));
        }
    }

    /**
     * العميل يشوف طلباته
     */
    public function index(Request $request)
    {
        $jobRequests = JobRequest::with('proposals.craftsman.user')
            ->where('client_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($jobRequests);
    }

    /**
     * الصنايعي يشوف الطلبات المتاحة له (نفس منطق الـ routing)
     */
    public function availableForCraftsman(Request $request)
    {
        $craftsmanProfile = $request->user()->craftsmanProfile;

        if (! $craftsmanProfile) {
            return response()->json(['message' => 'الحساب ده مش صنايعي'], 403);
        }

        $zoneIds = $craftsmanProfile->zones()->pluck('zones.id');

        $jobRequests = JobRequest::where('profession_id', $craftsmanProfile->profession_id)
            ->whereIn('zone_id', $zoneIds)
            ->where('status', 'open')
            ->latest()
            ->paginate(20);

        return response()->json($jobRequests);
    }
}
