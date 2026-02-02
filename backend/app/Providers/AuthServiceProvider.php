<?php

namespace App\Providers;

use App\Models\Exercise;
use App\Models\Invite;
use App\Models\Payment;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use App\Policies\ExercisePolicy;
use App\Policies\InvitePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\WorkoutDayPolicy;
use App\Policies\WorkoutItemPolicy;
use App\Policies\WorkoutPlanPolicy;
use App\Policies\WorkoutSessionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Exercise::class => ExercisePolicy::class,
        WorkoutPlan::class => WorkoutPlanPolicy::class,
        WorkoutDay::class => WorkoutDayPolicy::class,
        WorkoutItem::class => WorkoutItemPolicy::class,
        WorkoutSession::class => WorkoutSessionPolicy::class,
        Invite::class => InvitePolicy::class,
        Payment::class => PaymentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
