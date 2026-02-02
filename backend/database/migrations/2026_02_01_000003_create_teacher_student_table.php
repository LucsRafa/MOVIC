<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->index();
            $table->foreignId('student_id')->unique();
            $table->foreign('teacher_id', 'fk_teacher_student_teacher_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('student_id', 'fk_teacher_student_student_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_student');
    }
};
