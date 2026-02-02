<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Exercise;
use App\Models\User;

class ExercisePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function view(User $user, Exercise $exercise): bool
    {
        return $user->role === UserRole::Teacher && $exercise->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, Exercise $exercise): bool
    {
        return $user->role === UserRole::Teacher && $exercise->teacher_id === $user->id;
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        return $user->role === UserRole::Teacher && $exercise->teacher_id === $user->id;
    }
}
