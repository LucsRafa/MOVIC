<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\WorkoutItem;

class WorkoutItemPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, WorkoutItem $item): bool
    {
        return $user->role === UserRole::Teacher
            && $item->workoutDay
            && $item->workoutDay->workoutPlan
            && $item->workoutDay->workoutPlan->teacher_id === $user->id;
    }
}
