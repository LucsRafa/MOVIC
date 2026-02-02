<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('teacher_id');
            $table->foreignId('workout_plan_id');
            $table->foreignId('workout_day_id');
            $table->date('session_date');
            $table->string('status', 20)->default('in_progress');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->unique(['student_id', 'session_date']);
            $table->index(['student_id', 'session_date']);
            $table->index('teacher_id');
            $table->index('workout_plan_id');
            $table->index('workout_day_id');
            $table->timestamps();

            $table->foreign('student_id', 'fk_workout_sessions_student_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('teacher_id', 'fk_workout_sessions_teacher_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('workout_plan_id', 'fk_workout_sessions_plan_id')
                ->references('id')
                ->on('workout_plans')
                ->cascadeOnDelete();
            $table->foreign('workout_day_id', 'fk_workout_sessions_day_id')
                ->references('id')
                ->on('workout_days')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};
