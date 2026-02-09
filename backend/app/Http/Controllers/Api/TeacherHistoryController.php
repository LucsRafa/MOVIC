<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Services\TeacherHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherHistoryController extends Controller
{
    public function index(TeacherHistoryService $service): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $studentIds = TeacherStudent::where('teacher_id', $teacher->id)->pluck('student_id');
        $students = User::whereIn('id', $studentIds)->get();

        $data = $students->map(function ($student) use ($service) {
            $metrics = $service->buildForStudent($student->id);
            return array_merge([
                'student_id' => $student->id,
                'name' => $student->name,
                'avatar_url' => $student->avatar_url,
            ], $metrics);
        });

        return response()->json([
            'status' => 'success',
            'history' => $data,
        ]);
    }
}
