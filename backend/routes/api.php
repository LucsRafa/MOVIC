<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\StudentPaymentController;
use App\Http\Controllers\Api\StudentPlanController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\TeacherDashboardController;
use App\Http\Controllers\Api\TeacherHistoryController;
use App\Http\Controllers\Api\TeacherPaymentsController;
use App\Http\Controllers\Api\TeacherRequestsController;
use App\Http\Controllers\Api\TeacherStudentsController;
use App\Http\Controllers\Api\TeacherStudentController;
use App\Http\Controllers\Api\TeacherWorkoutsController;
use App\Http\Controllers\Api\TeacherWorkoutItemController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\WorkoutDayController;
use App\Http\Controllers\Api\WorkoutItemController;
use App\Http\Controllers\Api\WorkoutPlanController;
use App\Http\Controllers\Api\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

Route::post('forgot-password', [PasswordController::class, 'forgot'])->middleware('throttle:password-reset');
Route::post('reset-password', [PasswordController::class, 'reset'])->middleware('throttle:password-reset');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('forgot-password', [PasswordController::class, 'forgot'])->middleware('throttle:password-reset');
    Route::post('reset-password', [PasswordController::class, 'reset'])->middleware('throttle:password-reset');
});

Route::get('documentation', function () {
    return view('api-documentation');
});

Route::get('openapi.json', function () {
    $path = base_path('resources/openapi.json');
    return response()->file($path, [
        'Content-Type' => 'application/json'
    ]);
});

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'MOVIC API',
        'documentation' => url('/api/documentation')
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('workout-items/{itemId}/toggle', [WorkoutSessionController::class, 'toggle']);

    Route::patch('user/profile', [UserProfileController::class, 'updateProfile']);
    Route::patch('user/password', [UserProfileController::class, 'updatePassword']);
    Route::post('user/avatar', [UserProfileController::class, 'uploadAvatar']);
    Route::patch('me/profile', [UserProfileController::class, 'updateProfile']);
    Route::patch('me/password', [UserProfileController::class, 'updatePassword']);
    Route::post('me/avatar', [UserProfileController::class, 'uploadAvatar']);

    Route::prefix('teacher')->group(function () {
        Route::get('dashboard', [TeacherDashboardController::class, 'show']);
        Route::get('requests', [TeacherRequestsController::class, 'index']);
        Route::post('requests/{studentId}/approve', [TeacherRequestsController::class, 'approve']);
        Route::post('requests/{studentId}/reject', [TeacherRequestsController::class, 'reject']);

        Route::post('students/invite', [TeacherStudentController::class, 'invite']);
        Route::post('students/{id}/approve', [TeacherStudentController::class, 'approve']);

        Route::get('students', [TeacherStudentsController::class, 'index']);
        Route::get('students/{id}/overview', [TeacherStudentsController::class, 'overview']);
        Route::patch('students/{id}/status', [TeacherStudentsController::class, 'updateStatus']);
        Route::post('students/{id}/reset-password', [TeacherStudentsController::class, 'resetPassword']);
        Route::delete('students/{id}', [TeacherStudentsController::class, 'destroy']);

        Route::get('workouts', [TeacherWorkoutsController::class, 'index']);
        Route::post('workouts/days', [TeacherWorkoutsController::class, 'storeDay']);
        Route::post('workouts/days/{dayId}/items', [TeacherWorkoutItemController::class, 'store']);
        Route::put('workouts/items/{itemId}', [TeacherWorkoutItemController::class, 'update']);
        Route::delete('workouts/items/{itemId}', [TeacherWorkoutItemController::class, 'destroy']);

        Route::apiResource('exercises', ExerciseController::class)->names('teacher.exercises');

        Route::post('students/{studentId}/plans', [WorkoutPlanController::class, 'store']);
        Route::patch('plans/{plan}/activate', [WorkoutPlanController::class, 'activate']);
        Route::post('plans/{plan}/days', [WorkoutDayController::class, 'store']);
        Route::post('days/{day}/items', [WorkoutItemController::class, 'store']);

        Route::get('payments', [TeacherPaymentsController::class, 'index']);
        Route::post('payments/register', [TeacherPaymentsController::class, 'register']);
        Route::get('payments/{paymentId}/receipt.pdf', [TeacherPaymentsController::class, 'receiptPdf']);
        Route::post('payments/{paymentId}/send-receipt', [TeacherPaymentsController::class, 'sendReceipt']);

        Route::get('history', [TeacherHistoryController::class, 'index']);
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
