<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use App\Models\StandardizedServicePrice;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    // عرض قواعد التسعير (حسب المهنة)
    public function rules(Request $request)
    {
        $query = PricingRule::query();

        if ($request->has('profession_id')) {
            $query->where('profession_id', $request->profession_id);
        }

        return response()->json($query->get());
    }

    // عرض الخدمات الموحدة (سعر ثابت) - ممكن تتفلتر حسب المهنة
    public function standardizedServices(Request $request)
    {
        $query = StandardizedServicePrice::query();

        if ($request->has('profession_id')) {
            $query->where('profession_id', $request->profession_id);
        }

        return response()->json($query->get());
    }
}