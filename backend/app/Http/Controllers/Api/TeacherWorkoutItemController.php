<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\TeacherWorkoutItemStoreRequest;
use App\Http\Requests\Teacher\TeacherWorkoutItemUpdateRequest;
use App\Models\TeacherStudent;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherWorkoutItemController extends Controller
{
    public function store(TeacherWorkoutItemStoreRequest $request, int $dayId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $day = WorkoutDay::with('workoutPlan')->findOrFail($dayId);
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $day->workoutPlan->student_id)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $data = $request->validated();
        $item = WorkoutItem::create([
            'workout_day_id' => $day->id,
            'exercise_id' => $data['exercise_id'],
            'item_order' => $data['item_order'] ?? 1,
            'sets' => $data['sets'],
            'reps' => $data['reps'],
            'rest_seconds' => $data['rest_seconds'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exercício adicionado ao treino.',
            'item' => $item->load('exercise'),
        ], 201);
    }

    public function update(TeacherWorkoutItemUpdateRequest $request, int $itemId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $item = WorkoutItem::with('workoutDay.workoutPlan')->findOrFail($itemId);
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $item->workoutDay->workoutPlan->student_id)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $item->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Item atualizado com sucesso.',
            'item' => $item->load('exercise'),
        ]);
    }

    public function destroy(int $itemId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $item = WorkoutItem::with('workoutDay.workoutPlan')->findOrFail($itemId);
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $item->workoutDay->workoutPlan->student_id)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removido com sucesso.',
        ]);
    }
}
