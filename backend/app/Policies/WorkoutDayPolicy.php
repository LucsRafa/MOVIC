<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\WorkoutDay;

class WorkoutDayPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, WorkoutDay $day): bool
    {
        return $user->role === UserRole::Teacher
            && $day->workoutPlan
            && $day->workoutPlan->teacher_id === $user->id;
    }
}
