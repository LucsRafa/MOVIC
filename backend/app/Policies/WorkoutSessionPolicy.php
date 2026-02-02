<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutSession;

class WorkoutSessionPolicy
{
    public function view(User $user, WorkoutSession $session): bool
    {
        return $session->student_id === $user->id || $session->teacher_id === $user->id;
    }

    public function update(User $user, WorkoutSession $session): bool
    {
        return $session->student_id === $user->id || $session->teacher_id === $user->id;
    }
}
