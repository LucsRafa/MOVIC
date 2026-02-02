<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorkoutSessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\WorkoutItemCheckRequest;
use App\Http\Requests\Student\WorkoutSessionFinishRequest;
use App\Http\Requests\Student\WorkoutSessionStartRequest;
use App\Models\WorkoutItem;
use App\Models\WorkoutItemCheck;
use App\Models\WorkoutSession;
use App\Models\WorkoutDay;
use App\Services\WorkoutSessionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutSessionController extends Controller
{
    public function start(WorkoutSessionStartRequest $request, WorkoutSessionService $service): JsonResponse
    {
        $data = $request->validated();
        $day = WorkoutDay::with('workoutPlan')->findOrFail($data['workout_day_id']);

        if ($day->workoutPlan->student_id !== Auth::id()) {
            return response()->json(['message' => 'Not allowed'], 403);
        }

        $session = $service->startSession(Auth::user(), $day, $data['session_date'] ?? null);

        return response()->json(['session' => $session]);
    }

    public function check(WorkoutItemCheckRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $data = $request->validated();
        $item = WorkoutItem::findOrFail($data['workout_item_id']);

        if ($item->workout_day_id !== $session->workout_day_id) {
            return response()->json(['message' => 'Item not in session day'], 422);
        }

        $check = WorkoutItemCheck::updateOrCreate(
            [
                'workout_session_id' => $session->id,
                'workout_item_id' => $item->id,
            ],
            [
                'is_checked' => $data['is_checked'],
                'checked_at' => $data['is_checked'] ? Carbon::now() : null,
            ]
        );

        return response()->json(['check' => $check]);
    }

    public function finish(WorkoutSessionFinishRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $session->update([
            'status' => WorkoutSessionStatus::Completed,
            'finished_at' => Carbon::now(),
        ]);

        return response()->json(['session' => $session->fresh()]);
    }
}
