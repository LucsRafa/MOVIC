<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\TeacherStudentStatusRequest;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Models\WorkoutSession;
use App\Services\TeacherStudentOverviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class TeacherStudentsController extends Controller
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

        $search = request()->query('search');

        $studentIds = TeacherStudent::where('teacher_id', $teacher->id)
            ->pluck('student_id');

        $query = User::whereIn('id', $studentIds)->where('role', UserRole::Student->value);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query
            ->with('studentProfile')
            ->select('users.*')
            ->selectSub(
                WorkoutSession::select('session_date')
                    ->whereColumn('student_id', 'users.id')
                    ->orderByDesc('session_date')
                    ->limit(1),
                'last_workout_date'
            )
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'avatar_url' => $student->avatar_url,
                    'status' => $student->studentProfile?->status?->value ?? StudentStatus::Requested->value,
                    'last_workout_date' => $student->last_workout_date,
                    'trial_ends_at' => $student->studentProfile?->trial_ends_at?->toDateString(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'students' => $students,
        ]);
    }

    public function overview(int $studentId, TeacherStudentOverviewService $service): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $isLinked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$isLinked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $data = $service->build($studentId);

        return response()->json([
            'status' => 'success',
            'overview' => $data,
        ]);
    }

    public function updateStatus(TeacherStudentStatusRequest $request, int $studentId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $isLinked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$isLinked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $student = User::where('id', $studentId)->where('role', UserRole::Student->value)->firstOrFail();
        $student->studentProfile()->update([
            'status' => $request->validated()['status'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status do aluno atualizado com sucesso.',
        ]);
    }

    public function resetPassword(int $studentId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $isLinked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$isLinked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno não pertence a este professor.',
            ], 403);
        }

        $student = User::where('id', $studentId)->where('role', UserRole::Student->value)->firstOrFail();
        $status = Password::sendResetLink(['email' => $student->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível enviar o link de redefinição de senha.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Link de redefinição enviado para o aluno.',
        ]);
    }

    public function destroy(int $studentId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Aluno removido do professor com sucesso.',
        ]);
    }
}
