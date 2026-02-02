<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Invite;
use App\Models\User;

class InvitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, Invite $invite): bool
    {
        return $user->role === UserRole::Teacher && $invite->teacher_id === $user->id;
    }
}
