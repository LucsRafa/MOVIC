<?php

namespace Tests\Feature\Api;

use App\Enums\StudentStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMovicData;
use Tests\TestCase;

class TeacherDashboardFeatureTest extends TestCase
{
    use CreatesMovicData;
    use RefreshDatabase;

    public function test_teacher_dashboard_returns_expected_cards_and_badges(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-25 10:00:00'));

        try {
            $teacher = $this->actAsApiUser($this->createTeacher());
            $paidStudent = $this->createStudent([
                'email' => 'paid-student@example.com',
            ], StudentStatus::Active);
            $trialStudent = $this->createStudent([
                'email' => 'trial-student@example.com',
            ], StudentStatus::Trial);
            $this->createStudent([
                'email' => 'requested-student@example.com',
            ], StudentStatus::Requested);

            $this->linkTeacherAndStudent($teacher, $paidStudent);
            $this->linkTeacherAndStudent($teacher, $trialStudent);

            $plan = $this->createWorkoutPlan($teacher, $paidStudent, [
                'is_active' => true,
            ]);
            $this->createWorkoutDay($plan, [
                'weekday' => 1,
            ]);

            $this->createExercise($teacher, [
                'name' => 'Active Exercise',
                'is_active' => true,
            ]);
            $this->createExercise($teacher, [
                'name' => 'Inactive Exercise',
                'is_active' => false,
            ]);

            $this->createPayment($paidStudent, [
                'paid_at' => Carbon::now(),
            ]);

            $response = $this->getJson('/api/teacher/dashboard');

            $response->assertOk()
                ->assertJsonPath('status', 'success')
                ->assertJsonPath('data.cards.total_students', 2)
                ->assertJsonPath('data.cards.active_workouts', 1)
                ->assertJsonPath('data.cards.payments_ok', 2)
                ->assertJsonPath('data.cards.exercises_total', 1)
                ->assertJsonPath('data.badges.requests', 1);
        } finally {
            Carbon::setTestNow();
        }
    }
}
