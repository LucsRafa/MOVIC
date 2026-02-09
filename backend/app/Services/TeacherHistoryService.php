<?php

namespace App\Services;

use App\Enums\WorkoutSessionStatus;
use App\Models\WorkoutDay;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class TeacherHistoryService
{
    public function buildForStudent(int $studentId): array
    {
        $weeklyWorkouts = WorkoutDay::join('workout_plans', 'workout_plans.id', '=', 'workout_days.workout_plan_id')
            ->where('workout_plans.student_id', $studentId)
            ->where('workout_plans.is_active', true)
            ->count();

        $lastActivity = WorkoutSession::where('student_id', $studentId)
            ->orderByDesc('session_date')
            ->value('session_date');

        $totalCompleted = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->count();

        $monthlyPercent = $this->monthlyFrequencyPercent($studentId, $weeklyWorkouts);

        return [
            'last_activity' => $lastActivity ? Carbon::parse($lastActivity)->toDateString() : null,
            'weekly_workouts' => $weeklyWorkouts,
            'monthly_frequency_percent' => $monthlyPercent,
            'total_completed' => $totalCompleted,
        ];
    }

    private function monthlyFrequencyPercent(int $studentId, int $weeklyWorkouts): int
    {
        if ($weeklyWorkouts === 0) {
            return 0;
        }

        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();
        $weeksInMonth = (int) ceil($start->diffInDays($end) / 7);
        $planned = max(1, $weeksInMonth * $weeklyWorkouts);

        $completed = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->whereBetween('session_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $percent = (int) round(($completed / $planned) * 100);
        return max(0, min(100, $percent));
    }
}
