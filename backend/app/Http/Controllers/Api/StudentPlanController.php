<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentPlanController extends Controller
{
    public function active(): JsonResponse
    {
        $plan = WorkoutPlan::where('student_id', Auth::id())
            ->where('is_active', true)
            ->with(['days.items.exercise'])
            ->first();

        return response()->json([
            'status' => 'success',
            'plan' => $plan,
        ]);
    }
}
