<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->index();
            $table->unsignedTinyInteger('weekday');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->unique(['workout_plan_id', 'weekday']);
            $table->timestamps();

            $table->foreign('workout_plan_id', 'fk_workout_days_plan_id')
                ->references('id')
                ->on('workout_plans')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_days');
    }
};
