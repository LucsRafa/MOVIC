<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\TeacherWorkoutDayStoreRequest;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Models\WorkoutDay;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherWorkoutsController extends Controller
{
    public function index(): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $studentId = (int) request()->query('student_id');
        if (!$studentId) {
            return response()->json([
                'status' => 'error',
                'message' => 'student_id e obrigatorio.',
            ], 422);
        }

        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno nao pertence a este professor.',
            ], 403);
        }

        $student = User::findOrFail($studentId);
        $plan = WorkoutPlan::where('student_id', $studentId)
            ->where('is_active', true)
            ->first();

        $days = $plan
            ? $plan->days()->with(['items.exercise'])->orderBy('weekday')->get()
            : collect();

        return response()->json([
            'status' => 'success',
            'plan' => $plan,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'avatar_url' => $student->avatar_url,
            ],
            'days' => $days,
        ]);
    }

    public function storeDay(TeacherWorkoutDayStoreRequest $request): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $data = $request->validated();

        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $data['student_id'])
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno nao pertence a este professor.',
            ], 403);
        }

        $plan = WorkoutPlan::firstOrCreate(
            ['student_id' => $data['student_id'], 'is_active' => true],
            ['teacher_id' => $teacher->id, 'title' => 'Plano do aluno', 'published_at' => now()]
        );

        $exists = WorkoutDay::where('workout_plan_id', $plan->id)
            ->where('weekday', $data['weekday'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ja existe um treino cadastrado para este dia.',
            ], 422);
        }

        $day = WorkoutDay::create([
            'workout_plan_id' => $plan->id,
            'weekday' => $data['weekday'],
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Treino criado com sucesso.',
            'day' => $day,
        ], 201);
    }
}
