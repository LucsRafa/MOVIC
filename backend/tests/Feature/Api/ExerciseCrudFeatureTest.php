<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMovicData;
use Tests\TestCase;

class ExerciseCrudFeatureTest extends TestCase
{
    use CreatesMovicData;
    use RefreshDatabase;

    public function test_teacher_can_create_list_update_and_delete_an_exercise(): void
    {
        $teacher = $this->actAsApiUser($this->createTeacher());

        $createResponse = $this->postJson('/api/teacher/exercises', [
            'name' => 'Bench Press',
            'category' => 'Chest',
            'description' => 'Barbell press',
            'video_url' => 'https://example.com/bench.mp4',
            'is_active' => true,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('exercise.teacher_id', $teacher->id);

        $exerciseId = $createResponse->json('exercise.id');

        $this->getJson('/api/teacher/exercises')
            ->assertOk()
            ->assertJsonCount(1, 'exercises');

        $this->putJson("/api/teacher/exercises/{$exerciseId}", [
            'name' => 'Incline Bench Press',
            'category' => 'Chest',
            'description' => 'Updated',
            'video_url' => 'https://example.com/incline.mp4',
            'is_active' => false,
        ])->assertOk()
            ->assertJsonPath('exercise.name', 'Incline Bench Press')
            ->assertJsonPath('exercise.is_active', false);

        $this->deleteJson("/api/teacher/exercises/{$exerciseId}")
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('exercises', [
            'id' => $exerciseId,
        ]);
    }

    public function test_teacher_cannot_modify_another_teachers_exercise(): void
    {
        $owner = $this->createTeacher([
            'email' => 'owner@example.com',
        ]);
        $this->actAsApiUser($this->createTeacher([
            'email' => 'intruder@example.com',
        ]));

        $exercise = $this->createExercise($owner, [
            'name' => 'Deadlift',
        ]);

        $this->putJson("/api/teacher/exercises/{$exercise->id}", [
            'name' => 'Changed',
        ])->assertStatus(403);

        $this->deleteJson("/api/teacher/exercises/{$exercise->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'teacher_id' => $owner->id,
            'name' => 'Deadlift',
        ]);
    }
}
