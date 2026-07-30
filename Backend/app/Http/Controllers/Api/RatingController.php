<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // تقييم بعد انتهاء شغلانة (ثنائي الاتجاه: عميل يقيّم حرفي، أو العكس)
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|integer',
            'rated_user_id' => 'required|exists:users,id',
            'direction' => 'required|in:client_to_craftsman,craftsman_to_client',
            'score' => 'required|integer|min:1|max:5',
            'behavior_score' => 'nullable|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating = Rating::create([
            'job_id' => $request->job_id,
            'rated_by' => $request->user()->id,
            'rated_user_id' => $request->rated_user_id,
            'direction' => $request->direction,
            'score' => $request->score,
            'behavior_score' => $request->behavior_score,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'تم إرسال التقييم بنجاح',
            'rating' => $rating,
        ]);
    }

    // عرض كل تقييمات مستخدم معيّن (لعرضها في البروفايل)
    public function forUser(Request $request, $userId)
    {
        $ratings = Rating::where('rated_user_id', $userId)
            ->latest()
            ->paginate(20);

        $averageScore = Rating::where('rated_user_id', $userId)->avg('score');

        return response()->json([
            'average_score' => round($averageScore, 1),
            'ratings' => $ratings,
        ]);
    }
}