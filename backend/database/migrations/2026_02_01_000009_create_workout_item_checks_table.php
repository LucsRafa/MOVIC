<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_item_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id');
            $table->foreignId('workout_item_id');
            $table->boolean('is_checked')->default(false);
            $table->dateTime('checked_at')->nullable();
            $table->unique(['workout_session_id', 'workout_item_id']);
            $table->timestamps();

            $table->foreign('workout_session_id', 'fk_workout_item_checks_session_id')
                ->references('id')
                ->on('workout_sessions')
                ->cascadeOnDelete();
            $table->foreign('workout_item_id', 'fk_workout_item_checks_item_id')
                ->references('id')
                ->on('workout_items')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_item_checks');
    }
};
