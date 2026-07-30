<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class WarrantyClaimController extends Controller
{
    // عرض كل مطالبات الضمان الخاصة بالمستخدم الحالي
    public function index(Request $request)
    {
        $claims = WarrantyClaim::whereHas('job', function ($query) use ($request) {
            $query->where('client_id', $request->user()->id);
        })->latest()->paginate(20);

        return response()->json($claims);
    }

    // تقديم مطالبة ضمان جديدة
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|integer',
            'issue_description' => 'required|string|max:1000',
            'claim_type' => 'required|in:quality_warranty,accidental_damage',
        ]);

        $claim = WarrantyClaim::create([
            'job_id' => $request->job_id,
            'issue_description' => $request->issue_description,
            'claim_type' => $request->claim_type,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'تم تسجيل مطالبة الضمان بنجاح، سيتم مراجعتها',
            'claim' => $claim,
        ]);
    }

    // عرض تفاصيل مطالبة معينة
    public function show(WarrantyClaim $claim)
    {
        return response()->json($claim);
    }
}