<?php

namespace Tests\Feature\Api;

use App\Enums\WorkoutSessionStatus;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMovicData;
use Tests\TestCase;

class TeacherWorkoutsFeatureTest extends TestCase
{
    use CreatesMovicData;
    use RefreshDatabase;

    public function test_teacher_can_create_day_and_manage_items_for_a_linked_student(): void
    {
        $teacher = $this->actAsApiUser($this->createTeacher());
        $student = $this->createStudent();
        $this->linkTeacherAndStudent($teacher, $student);
        $exercise = $this->createExercise($teacher);

        $dayResponse = $this->postJson('/api/teacher/workouts/days', [
            'student_id' => $student->id,
            'weekday' => 2,
            'title' => 'Treino B',
            'notes' => 'Volume day',
        ]);

        $dayResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('day.weekday', 2);

        $dayId = $dayResponse->json('day.id');

        $itemResponse = $this->postJson("/api/teacher/workouts/days/{$dayId}/items", [
            'exercise_id' => $exercise->id,
            'item_order' => 1,
            'sets' => 4,
            'reps' => '12',
            'rest_seconds' => 90,
            'notes' => 'Controlled tempo',
        ]);

        $itemResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('item.exercise.id', $exercise->id);

        $itemId = $itemResponse->json('item.id');

        $this->putJson("/api/teacher/workouts/items/{$itemId}", [
            'sets' => 5,
            'reps' => '10',
            'rest_seconds' => 60,
        ])->assertOk()
            ->assertJsonPath('item.sets', 5)
            ->assertJsonPath('item.reps', '10');

        $this->deleteJson("/api/teacher/workouts/items/{$itemId}")
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('workout_items', [
            'id' => $itemId,
        ]);
    }

    public function test_student_can_toggle_items_persist_state_and_finish_with_average_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-25 10:00:00'));

        try {
            $teacher = $this->createTeacher();
            $student = $this->actAsApiUser($this->createStudent());
            $this->linkTeacherAndStudent($teacher, $student);

            $exercise = $this->createExercise($teacher);
            $plan = $this->createWorkoutPlan($teacher, $student);
            $day = $this->createWorkoutDay($plan, [
                'weekday' => Carbon::now()->dayOfWeek,
            ]);
            $item = $this->createWorkoutItem($day, $exercise);

            $toggleResponse = $this->postJson("/api/workout-items/{$item->id}/toggle");

            $toggleResponse->assertOk()
                ->assertJsonPath('status', 'success')
                ->assertJsonPath('session.workout_day_id', $day->id)
                ->assertJsonPath('check.is_checked', true);

            $sessionId = $toggleResponse->json('session.id');

            $this->getJson('/api/student/plan/active')
                ->assertOk()
                ->assertJsonPath('plan.days.0.items.0.is_checked', true);

            Carbon::setTestNow(Carbon::parse('2026-04-25 10:32:00'));

            $this->postJson("/api/student/sessions/{$sessionId}/finish")
                ->assertOk()
                ->assertJsonPath('status', 'success')
                ->assertJsonPath('session.status', WorkoutSessionStatus::Completed->value);

            $this->assertDatabaseHas('workout_item_checks', [
                'workout_session_id' => $sessionId,
                'workout_item_id' => $item->id,
                'is_checked' => true,
            ]);

            $this->assertDatabaseHas('workout_sessions', [
                'id' => $sessionId,
                'status' => WorkoutSessionStatus::Completed->value,
            ]);

            $session = WorkoutSession::find($sessionId);
            $this->assertNotNull($session?->started_at);
            $this->assertNotNull($session?->finished_at);

            $this->getJson('/api/student/dashboard')
                ->assertOk()
                ->assertJsonPath('summary.avg_minutes', 32)
                ->assertJsonPath('summary.avg_workout_minutes', 32);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_student_can_reopen_a_finished_session_by_toggling_an_item(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-25 10:00:00'));

        try {
            $teacher = $this->createTeacher();
            $student = $this->actAsApiUser($this->createStudent());
            $this->linkTeacherAndStudent($teacher, $student);

            $exercise = $this->createExercise($teacher);
            $plan = $this->createWorkoutPlan($teacher, $student);
            $day = $this->createWorkoutDay($plan, [
                'weekday' => Carbon::now()->dayOfWeek,
            ]);
            $item = $this->createWorkoutItem($day, $exercise);

            $toggleResponse = $this->postJson("/api/workout-items/{$item->id}/toggle");
            $sessionId = $toggleResponse->json('session.id');

            Carbon::setTestNow(Carbon::parse('2026-04-25 10:20:00'));

            $this->postJson("/api/student/sessions/{$sessionId}/finish")
                ->assertOk()
                ->assertJsonPath('status', 'success');

            Carbon::setTestNow(Carbon::parse('2026-04-25 10:25:00'));

            $this->postJson("/api/workout-items/{$item->id}/toggle")
                ->assertOk()
                ->assertJsonPath('status', 'success')
                ->assertJsonPath('session.id', $sessionId)
                ->assertJsonPath('session.status', WorkoutSessionStatus::InProgress->value)
                ->assertJsonPath('check.is_checked', false);

            $this->assertDatabaseHas('workout_sessions', [
                'id' => $sessionId,
                'status' => WorkoutSessionStatus::InProgress->value,
                'finished_at' => null,
            ]);
        } finally {
            Carbon::setTestNow();
        }
    }
}
