<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorkoutSessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\WorkoutItemCheckRequest;
use App\Http\Requests\Student\WorkoutSessionFinishRequest;
use App\Http\Requests\Student\WorkoutSessionStartRequest;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use App\Models\WorkoutItemCheck;
use App\Models\WorkoutSession;
use App\Services\WorkoutSessionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutSessionController extends Controller
{
    public function toggle(int $itemId, WorkoutSessionService $service): JsonResponse
    {
        $data = request()->validate([
            'session_date' => ['nullable', 'date'],
        ]);

        $item = WorkoutItem::with('workoutDay.workoutPlan')->findOrFail($itemId);
        $day = $item->workoutDay;

        if (!$day || !$day->workoutPlan || $day->workoutPlan->student_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não permitido para este treino.',
            ], 403);
        }

        $session = $service->startSession(Auth::user(), $day, $data['session_date'] ?? null);

        if ($session->workout_day_id !== $item->workout_day_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Já existe outro treino iniciado para esta data.',
            ], 422);
        }

        $session = $service->reopenSession($session);

        $check = WorkoutItemCheck::firstOrNew([
            'workout_session_id' => $session->id,
            'workout_item_id' => $item->id,
        ]);

        $isChecked = !$check->exists || !$check->is_checked;
        $check->fill([
            'is_checked' => $isChecked,
            'checked_at' => $isChecked ? Carbon::now() : null,
        ])->save();

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
            'check' => $this->serializeCheck($check->fresh()),
        ]);
    }

    public function start(WorkoutSessionStartRequest $request, WorkoutSessionService $service): JsonResponse
    {
        $data = $request->validated();
        $day = WorkoutDay::with('workoutPlan')->findOrFail($data['workout_day_id']);

        if ($day->workoutPlan->student_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não permitido para este treino.',
            ], 403);
        }

        $session = $service->startSession(Auth::user(), $day, $data['session_date'] ?? null);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);
    }

    public function check(
        WorkoutItemCheckRequest $request,
        WorkoutSession $session,
        WorkoutSessionService $service
    ): JsonResponse {
        $this->authorize('update', $session);
        $session = $service->reopenSession($session);

        $data = $request->validated();
        $item = WorkoutItem::findOrFail($data['workout_item_id']);

        if ($item->workout_day_id !== $session->workout_day_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'O exercício não pertence a este treino.',
            ], 422);
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

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
            'check' => $this->serializeCheck($check->fresh()),
        ]);
    }

    public function finish(WorkoutSessionFinishRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $session->update([
            'started_at' => $session->started_at ?? Carbon::now(),
            'status' => WorkoutSessionStatus::Completed,
            'finished_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Treino finalizado com sucesso.',
            'session' => $session->fresh(),
        ]);
    }

    private function serializeCheck(WorkoutItemCheck $check): array
    {
        $completedAt = $check->checked_at?->toIso8601String();

        return [
            'id' => $check->id,
            'workout_session_id' => $check->workout_session_id,
            'workout_item_id' => $check->workout_item_id,
            'is_checked' => $check->is_checked,
            'checked_at' => $completedAt,
            'completed_at' => $completedAt,
        ];
    }
}
