<?php

namespace App\Services;

use App\Enums\WorkoutSessionStatus;
use App\Models\User;
use App\Models\WorkoutDay;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class WorkoutSessionService
{
    public function startSession(User $student, WorkoutDay $day, ?string $sessionDate = null): WorkoutSession
    {
        $date = $sessionDate ? Carbon::parse($sessionDate)->toDateString() : Carbon::today()->toDateString();

        $session = WorkoutSession::firstOrCreate(
            ['student_id' => $student->id, 'session_date' => $date],
            [
                'teacher_id' => $day->workoutPlan->teacher_id,
                'workout_plan_id' => $day->workout_plan_id,
                'workout_day_id' => $day->id,
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
            ]
        );

        if (!$session->started_at) {
            $session->forceFill([
                'started_at' => Carbon::now(),
            ])->save();
        }

        return $session->fresh();
    }

    public function reopenSession(WorkoutSession $session): WorkoutSession
    {
        if ($session->status !== WorkoutSessionStatus::Completed && !$session->finished_at) {
            return $session;
        }

        $session->forceFill([
            'status' => WorkoutSessionStatus::InProgress,
            'finished_at' => null,
            'started_at' => $session->started_at ?? Carbon::now(),
        ])->save();

        return $session->fresh();
    }
}
