<?php

namespace App\Services;

use App\Enums\StudentStatus;
use App\Enums\WorkoutSessionStatus;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\WorkoutDay;
use App\Models\WorkoutItemCheck;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class StudentDashboardService
{
    public function build(int $studentId): array
    {
        $user = auth()->user();
        $plan = WorkoutPlan::where('student_id', $studentId)->where('is_active', true)->first();
        $weeklyWorkoutsTotal = $plan ? $plan->days()->count() : 0;

        $todayWeekday = Carbon::now()->dayOfWeek; // 0..6
        $todayDay = $plan ? WorkoutDay::where('workout_plan_id', $plan->id)->where('weekday', $todayWeekday)->first() : null;

        $todaySession = WorkoutSession::where('student_id', $studentId)
            ->where('session_date', Carbon::today()->toDateString())
            ->first();

        $todayTotal = $todayDay ? $todayDay->items()->count() : 0;
        $todayDone = $todaySession
            ? WorkoutItemCheck::where('workout_session_id', $todaySession->id)->where('is_checked', true)->count()
            : 0;

        $avgMinutes = $this->averageWorkoutMinutes($studentId);
        $progressPercent = $todayTotal > 0 ? (int) round(($todayDone / $todayTotal) * 100) : 0;
        $estimatedRemaining = $avgMinutes !== null ? (int) round($avgMinutes * (1 - ($progressPercent / 100))) : null;

        $subscription = $this->buildSubscription($studentId, $plan);
        $week = $this->buildWeekSummary($studentId, $plan, $weeklyWorkoutsTotal);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'role' => $user->role,
            ],
            'subscription' => $subscription,
            'summary' => [
                'weekly_workouts_total' => $weeklyWorkoutsTotal,
                'streak_days' => $this->calculateStreak($studentId),
                'today_exercises_done' => $todayDone,
                'today_exercises_total' => $todayTotal,
                'avg_workout_minutes' => $avgMinutes,
                'avg_minutes' => $avgMinutes,
                'estimated_finish_minutes' => $estimatedRemaining,
            ],
            'today' => [
                'weekday' => $todayWeekday,
                'workout_day' => $this->buildTodayWorkout($todayDay, $todaySession),
                'session' => $todaySession,
            ],
            'week' => $week,
        ];
    }

    private function buildSubscription(int $studentId, ?WorkoutPlan $plan): array
    {
        $profile = StudentProfile::where('user_id', $studentId)->first();
        $status = $profile?->status?->value ?? StudentStatus::Requested->value;
        if ($status === StudentStatus::Trial->value) {
            $status = 'experimental';
        }
        $expiresAt = $profile?->trial_ends_at?->toDateString();

        return [
            'status' => $status,
            'expires_at' => $expiresAt,
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->title,
                'price_cents' => 15000,
                'currency' => 'BRL',
                'billing_interval' => 'month',
            ] : null,
        ];
    }

    private function buildTodayWorkout(?WorkoutDay $day, ?WorkoutSession $session): ?array
    {
        if (!$day) {
            return null;
        }

        $items = $day->items()->with('exercise')->orderBy('item_order')->get();
        $checksByItemId = $session
            ? WorkoutItemCheck::where('workout_session_id', $session->id)
                ->where('is_checked', true)
                ->get()
                ->keyBy('workout_item_id')
            : collect();

        $mapped = $items->map(function ($item) use ($checksByItemId) {
            $check = $checksByItemId->get($item->id);
            $completedAt = $check?->checked_at?->toIso8601String();

            return [
                'workout_item_id' => $item->id,
                'order' => $item->item_order,
                'exercise' => [
                    'id' => $item->exercise->id,
                    'name' => $item->exercise->name,
                    'video_url' => $item->exercise->video_url,
                    'thumbnail_url' => $item->exercise->thumbnail_url,
                    'description' => $item->exercise->description,
                ],
                'sets' => $item->sets,
                'reps' => $item->reps,
                'rest_seconds' => $item->rest_seconds,
                'completed_at' => $completedAt,
                'is_checked' => (bool) $completedAt,
            ];
        });

        $progressPercent = $items->count() > 0
            ? (int) round(($mapped->where('is_checked', true)->count() / $items->count()) * 100)
            : 0;

        return [
            'id' => $day->id,
            'title' => $day->title,
            'subtitle' => $day->notes ?? null,
            'items' => $mapped,
            'progress_percent' => $progressPercent,
        ];
    }

    private function buildWeekSummary(int $studentId, ?WorkoutPlan $plan, int $weeklyWorkoutsTotal): array
    {
        $days = collect();
        if ($plan) {
            $planDays = $plan->days()->get();
            $days = $planDays->map(function ($day) use ($studentId) {
                $session = WorkoutSession::where('student_id', $studentId)
                    ->where('workout_day_id', $day->id)
                    ->whereDate('session_date', '<=', Carbon::today())
                    ->orderByDesc('session_date')
                    ->first();

                $status = 'pending';
                if ($session && $session->status === WorkoutSessionStatus::Completed) {
                    $status = 'completed';
                } elseif ($day->weekday < Carbon::today()->dayOfWeek) {
                    $status = 'missed';
                } elseif ($day->weekday > Carbon::today()->dayOfWeek) {
                    $status = 'future';
                }

                return [
                    'weekday' => $day->weekday,
                    'title' => $day->title,
                    'status' => $status,
                    'session_date' => $session?->session_date?->toDateString(),
                ];
            });
        }

        $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $weeklyDone = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->whereBetween('session_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $monthlyPercent = $this->monthlyFrequencyPercent($studentId, $weeklyWorkoutsTotal);

        return [
            'days' => $days,
            'monthly_frequency_percent' => $monthlyPercent,
            'weekly_goal' => [
                'done' => $weeklyDone,
                'total' => $weeklyWorkoutsTotal,
            ],
        ];
    }

    private function calculateStreak(int $studentId): int
    {
        $sessions = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->orderByDesc('session_date')
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $expected = $sessions->first()->session_date->isSameDay($today) ? $today : $today->copy()->subDay();

        $streak = 0;
        foreach ($sessions as $session) {
            if ($session->session_date->isSameDay($expected)) {
                $streak++;
                $expected = $expected->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    private function averageWorkoutMinutes(int $studentId): ?int
    {
        $sessions = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->orderByDesc('session_date')
            ->limit(10)
            ->get();

        if ($sessions->isEmpty()) {
            return null;
        }

        $avg = $sessions->avg(function ($session) {
            return Carbon::parse($session->finished_at)->diffInMinutes($session->started_at);
        });

        return $avg ? (int) round($avg) : null;
    }

    private function monthlyFrequencyPercent(int $studentId, int $weeklyWorkoutsTotal): int
    {
        if ($weeklyWorkoutsTotal === 0) {
            return 0;
        }

        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();
        $weeksInMonth = (int) ceil($start->diffInDays($end) / 7);
        $planned = max(1, $weeksInMonth * $weeklyWorkoutsTotal);

        $completed = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->whereBetween('session_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $percent = (int) round(($completed / $planned) * 100);
        return max(0, min(100, $percent));
    }
}
