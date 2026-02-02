<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_day_id')->index();
            $table->foreignId('exercise_id')->index();
            $table->unsignedInteger('item_order')->default(1);
            $table->unsignedInteger('sets');
            $table->string('reps', 50);
            $table->unsignedInteger('rest_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('workout_day_id', 'fk_workout_items_day_id')
                ->references('id')
                ->on('workout_days')
                ->cascadeOnDelete();
            $table->foreign('exercise_id', 'fk_workout_items_exercise_id')
                ->references('id')
                ->on('exercises')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_items');
    }
};
