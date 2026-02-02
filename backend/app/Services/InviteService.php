<?php

namespace App\Services;

use App\Models\Invite;
use App\Models\User;
use App\Notifications\InviteNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InviteService
{
    public function createInvite(User $teacher, string $email): Invite
    {
        $invite = Invite::create([
            'teacher_id' => $teacher->id,
            'email' => $email,
            'token' => Str::random(40),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        Notification::route('mail', $email)->notify(new InviteNotification($invite));

        return $invite;
    }
}
