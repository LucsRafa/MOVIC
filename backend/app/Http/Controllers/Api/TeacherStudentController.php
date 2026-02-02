<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ApproveStudentRequest;
use App\Http\Requests\Teacher\InviteStudentRequest;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Services\InviteService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function invite(InviteStudentRequest $request, InviteService $service): JsonResponse
    {
        $invite = $service->createInvite(Auth::user(), $request->validated()['email']);

        return response()->json(['invite' => $invite], 201);
    }

    public function index(): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json(['message' => 'Not allowed'], 403);
        }

        $students = $teacher->students()->with('studentProfile')->get();

        return response()->json(['students' => $students]);
    }

    public function approve(ApproveStudentRequest $request, int $id): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json(['message' => 'Not allowed'], 403);
        }
        $student = User::where('id', $id)->where('role', UserRole::Student)->firstOrFail();

        TeacherStudent::updateOrCreate(
            ['student_id' => $student->id],
            ['teacher_id' => $teacher->id]
        );

        $status = $request->validated()['status'] ?? StudentStatus::Active->value;

        $student->studentProfile()->update([
            'status' => $status,
            'approved_at' => Carbon::now(),
        ]);

        return response()->json(['student' => $student->load('studentProfile')]);
    }
}
