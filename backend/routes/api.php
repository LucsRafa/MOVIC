<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\StudentPaymentController;
use App\Http\Controllers\Api\StudentPlanController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\TeacherStudentController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\WorkoutDayController;
use App\Http\Controllers\Api\WorkoutItemController;
use App\Http\Controllers\Api\WorkoutPlanController;
use App\Http\Controllers\Api\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::patch('user/profile', [UserProfileController::class, 'updateProfile']);
    Route::patch('user/password', [UserProfileController::class, 'updatePassword']);
    Route::post('user/avatar', [UserProfileController::class, 'uploadAvatar']);

    Route::prefix('teacher')->group(function () {
        Route::post('students/invite', [TeacherStudentController::class, 'invite']);
        Route::get('students', [TeacherStudentController::class, 'index']);
        Route::post('students/{id}/approve', [TeacherStudentController::class, 'approve']);

        Route::apiResource('exercises', ExerciseController::class)->names('teacher.exercises');

        Route::post('students/{studentId}/plans', [WorkoutPlanController::class, 'store']);
        Route::patch('plans/{plan}/activate', [WorkoutPlanController::class, 'activate']);
        Route::post('plans/{plan}/days', [WorkoutDayController::class, 'store']);
        Route::post('days/{day}/items', [WorkoutItemController::class, 'store']);
    });

    Route::prefix('student')->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'show']);
        Route::get('plan/active', [StudentPlanController::class, 'active']);
        Route::post('sessions/start', [WorkoutSessionController::class, 'start']);
        Route::post('sessions/{session}/check', [WorkoutSessionController::class, 'check']);
        Route::post('sessions/{session}/finish', [WorkoutSessionController::class, 'finish']);

        Route::get('payments', [StudentPaymentController::class, 'index']);
        Route::get('payments/{id}', [StudentPaymentController::class, 'show']);
        Route::get('payments/{id}/pdf', [StudentPaymentController::class, 'pdf']);
        Route::post('payments/{id}/email', [StudentPaymentController::class, 'email']);
        Route::post('payments/manual', [StudentPaymentController::class, 'manual']);
    });
});
