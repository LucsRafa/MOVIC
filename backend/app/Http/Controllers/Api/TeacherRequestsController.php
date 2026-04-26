<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\StudentProfile;
use App\Models\TeacherStudent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherRequestsController extends Controller
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

        $requests = StudentProfile::where('status', StudentStatus::Requested->value)
            ->whereHas('user', function ($query) {
                $query->where('role', UserRole::Student->value);
            })
            ->whereDoesntHave('user.teachers')
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($profile) {
                return [
                    'student_id' => $profile->user_id,
                    'name' => $profile->user?->name,
                    'email' => $profile->user?->email,
                    'phone' => $profile->user?->phone,
                    'requested_at' => $profile->created_at?->toDateString(),
                    'status' => $profile->status?->value,
                ];
            });

        return response()->json([
            'status' => 'success',
            'requests' => $requests,
        ]);
    }

    public function approve(int $studentId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $student = User::where('id', $studentId)
            ->where('role', UserRole::Student->value)
            ->with('studentProfile')
            ->firstOrFail();

        if ($student->studentProfile?->status !== StudentStatus::Requested) {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta solicitação não está mais pendente.',
            ], 422);
        }

        $alreadyLinked = TeacherStudent::where('student_id', $student->id)->exists();
        if ($alreadyLinked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este aluno já possui professor vinculado.',
            ], 422);
        }

        TeacherStudent::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
        ]);

        $trialDays = Plan::where('is_active', true)->value('trial_days') ?? 7;
        $student->studentProfile()->update([
            'status' => StudentStatus::Trial->value,
            'trial_ends_at' => Carbon::now()->addDays($trialDays),
            'approved_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Aluno aprovado e período experimental iniciado.',
        ]);
    }

    public function reject(int $studentId): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $student = User::where('id', $studentId)
            ->where('role', UserRole::Student->value)
            ->with('studentProfile')
            ->firstOrFail();

        if ($student->studentProfile?->status !== StudentStatus::Requested) {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta solicitação não está mais pendente.',
            ], 422);
        }

        $student->studentProfile()->update([
            'status' => StudentStatus::Inactive->value,
            'notes' => 'Rejeitado em ' . Carbon::now()->format('d/m/Y H:i'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Solicitação rejeitada com sucesso.',
        ]);
    }
}
