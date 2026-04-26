<?php

namespace Tests\Support;

use App\Enums\PaymentMethod;
use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Exercise;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use App\Models\WorkoutPlan;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

trait CreatesMovicData
{
    protected function actAsApiUser(User $user): User
    {
        Sanctum::actingAs($user);

        return $user;
    }

    protected function createTeacher(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::Teacher->value,
        ], $attributes));

        TeacherProfile::firstOrCreate(['user_id' => $user->id]);

        return $user->fresh();
    }

    protected function createStudent(array $attributes = [], StudentStatus $status = StudentStatus::Requested): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::Student->value,
        ], $attributes));

        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => $status->value,
                'trial_ends_at' => $status === StudentStatus::Trial ? Carbon::now()->addDays(7) : null,
                'approved_at' => $status === StudentStatus::Trial ? Carbon::now() : null,
            ]
        );

        return $user->fresh();
    }

    protected function linkTeacherAndStudent(User $teacher, User $student): TeacherStudent
    {
        return TeacherStudent::firstOrCreate([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
        ]);
    }

    protected function createSubscriptionPlan(array $attributes = []): Plan
    {
        return Plan::create(array_merge([
            'name' => 'Plano Mensal',
            'price_cents' => 15000,
            'currency' => 'BRL',
            'billing_interval' => 'month',
            'trial_days' => 7,
            'is_active' => true,
        ], $attributes));
    }

    protected function createExercise(User $teacher, array $attributes = []): Exercise
    {
        return Exercise::create(array_merge([
            'teacher_id' => $teacher->id,
            'name' => 'Squat',
            'category' => 'Legs',
            'description' => 'Compound movement',
            'video_url' => 'https://example.com/video.mp4',
            'thumbnail_url' => null,
            'is_active' => true,
        ], $attributes));
    }

    protected function createWorkoutPlan(User $teacher, User $student, array $attributes = []): WorkoutPlan
    {
        return WorkoutPlan::create(array_merge([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'title' => 'Plano base',
            'is_active' => true,
            'published_at' => Carbon::now(),
        ], $attributes));
    }

    protected function createWorkoutDay(WorkoutPlan $plan, array $attributes = []): WorkoutDay
    {
        return WorkoutDay::create(array_merge([
            'workout_plan_id' => $plan->id,
            'weekday' => 1,
            'title' => 'Treino A',
            'notes' => 'Notas',
        ], $attributes));
    }

    protected function createWorkoutItem(WorkoutDay $day, Exercise $exercise, array $attributes = []): WorkoutItem
    {
        return WorkoutItem::create(array_merge([
            'workout_day_id' => $day->id,
            'exercise_id' => $exercise->id,
            'item_order' => 1,
            'sets' => 4,
            'reps' => '10',
            'rest_seconds' => 60,
            'notes' => null,
        ], $attributes));
    }

    protected function createPayment(User $student, array $attributes = []): Payment
    {
        return Payment::create(array_merge([
            'student_id' => $student->id,
            'plan_id' => null,
            'provider' => PaymentProvider::Manual->value,
            'method' => PaymentMethod::Pix->value,
            'amount_cents' => 15000,
            'currency' => 'BRL',
            'status' => PaymentStatus::Paid->value,
            'description' => 'Mensalidade',
            'paid_at' => Carbon::now(),
            'transaction_id' => 'txn-001',
        ], $attributes));
    }
}
