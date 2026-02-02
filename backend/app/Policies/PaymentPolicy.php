<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $payment->student_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->studentProfile !== null;
    }
}
