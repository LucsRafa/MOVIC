<?php

namespace App\Services;

use App\Enums\StudentStatus;
use App\Models\Exercise;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\TeacherStudent;
use App\Models\WorkoutDay;
use Carbon\Carbon;

class TeacherDashboardService
{
    public function build(int $teacherId): array
    {
        $totalStudents = TeacherStudent::where('teacher_id', $teacherId)->count();
        $exercisesTotal = Exercise::where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->count();

        $activeWorkouts = WorkoutDay::join('workout_plans', 'workout_plans.id', '=', 'workout_days.workout_plan_id')
            ->where('workout_plans.teacher_id', $teacherId)
            ->where('workout_plans.is_active', true)
            ->count();

        $requests = StudentProfile::where('status', StudentStatus::Requested->value)
            ->whereHas('user', function ($query) {
                $query->where('role', 'student');
            })
            ->whereDoesntHave('user.teachers')
            ->count();

        $paymentsOk = $this->countPaymentsOk($teacherId);

        return [
            'cards' => [
                'total_students' => $totalStudents,
                'active_workouts' => $activeWorkouts,
                'payments_ok' => $paymentsOk,
                'exercises_total' => $exercisesTotal,
            ],
            'badges' => [
                'requests' => $requests,
            ],
        ];
    }

    private function countPaymentsOk(int $teacherId): int
    {
        $month = Carbon::now()->format('Y-m');

        $paidInMonth = Payment::join('teacher_student', 'teacher_student.student_id', '=', 'payments.student_id')
            ->where('teacher_student.teacher_id', $teacherId)
            ->where('payments.status', 'paid')
            ->whereRaw("DATE_FORMAT(payments.paid_at, '%Y-%m') = ?", [$month])
            ->select('payments.student_id')
            ->distinct()
            ->pluck('payments.student_id')
            ->all();

        $trialOk = StudentProfile::join('teacher_student', 'teacher_student.student_id', '=', 'student_profiles.user_id')
            ->where('teacher_student.teacher_id', $teacherId)
            ->where('student_profiles.status', StudentStatus::Trial->value)
            ->whereNotNull('student_profiles.trial_ends_at')
            ->where('student_profiles.trial_ends_at', '>=', Carbon::now())
            ->pluck('student_profiles.user_id')
            ->all();

        return count(array_unique(array_merge($paidInMonth, $trialOk)));
    }
}
