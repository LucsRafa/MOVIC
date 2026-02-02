<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkoutDay;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class WorkoutSessionService
{
    public function startSession(User $student, WorkoutDay $day, ?string $sessionDate = null): WorkoutSession
    {
        $date = $sessionDate ? Carbon::parse($sessionDate)->toDateString() : Carbon::today()->toDateString();

        return WorkoutSession::firstOrCreate(
            ['student_id' => $student->id, 'session_date' => $date],
            [
                'teacher_id' => $day->workoutPlan->teacher_id,
                'workout_plan_id' => $day->workout_plan_id,
                'workout_day_id' => $day->id,
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
            ]
        );
    }
}
