<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CraftsmanProfileController extends Controller
{
    /**
     * إكمال بيانات الصنايعي بعد التسجيل: المهنة، سنين الخبرة، المناطق اللي بيشتغل فيها
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profession_id' => 'required|exists:professions,id',
            'zone_ids' => 'required|array|min:1',
            'zone_ids.*' => 'exists:zones,id',
            'years_experience' => 'required|integer|min:0',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if ($user->role !== 'craftsman') {
            return response()->json(['message' => 'الحساب ده مش صنايعي'], 403);
        }

        $profile = CraftsmanProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'profession_id' => $request->profession_id,
                'years_experience' => $request->years_experience,
                'bio' => $request->bio,
            ]
        );

        $profile->zones()->sync($request->zone_ids);

        return response()->json([
            'message' => 'تم حفظ بيانات الصنايعي',
            'profile' => $profile->load('zones', 'profession'),
        ]);
    }

    public function show(Request $request)
    {
        $profile = CraftsmanProfile::with('zones', 'profession', 'verificationDocuments')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($profile);
    }
}
