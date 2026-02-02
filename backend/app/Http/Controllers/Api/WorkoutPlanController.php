<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\WorkoutPlanStoreRequest;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Services\WorkoutPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutPlanController extends Controller
{
    public function store(WorkoutPlanStoreRequest $request, int $studentId): JsonResponse
    {
        $teacher = Auth::user();

        $link = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$link) {
            return response()->json(['message' => 'Student not linked'], 403);
        }

        $student = User::findOrFail($studentId);

        $plan = WorkoutPlan::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => $request->validated()['title'],
            'is_active' => $request->validated()['is_active'] ?? true,
        ]);

        return response()->json(['plan' => $plan], 201);
    }

    public function activate(WorkoutPlan $plan, WorkoutPlanService $service): JsonResponse
    {
        $this->authorize('update', $plan);

        $service->activatePlan($plan);

        return response()->json(['plan' => $plan->fresh()]);
    }
}
