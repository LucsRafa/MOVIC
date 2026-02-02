<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->index();
            $table->foreignId('teacher_id')->index();
            $table->string('title');
            $table->boolean('is_active')->default(true)->index();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id', 'fk_workout_plans_student_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('teacher_id', 'fk_workout_plans_teacher_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_plans');
    }
};
