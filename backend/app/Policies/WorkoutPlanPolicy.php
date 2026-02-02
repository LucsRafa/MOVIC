<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\WorkoutPlan;

class WorkoutPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function view(User $user, WorkoutPlan $plan): bool
    {
        return $user->role === UserRole::Teacher && $plan->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, WorkoutPlan $plan): bool
    {
        return $user->role === UserRole::Teacher && $plan->teacher_id === $user->id;
    }
}
