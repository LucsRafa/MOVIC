<?php

namespace App\Services;

use App\Enums\WorkoutSessionStatus;
use App\Models\WorkoutItemCheck;
use App\Models\WorkoutSession;
use Carbon\Carbon;

class TeacherStudentOverviewService
{
    public function build(int $studentId): array
    {
        $sessions = WorkoutSession::with(['workoutDay.items.exercise'])
            ->where('student_id', $studentId)
            ->orderByDesc('session_date')
            ->limit(10)
            ->get();

        $totalCompleted = WorkoutSession::where('student_id', $studentId)
            ->where('status', WorkoutSessionStatus::Completed)
            ->count();

        $lastWorkout = WorkoutSession::where('student_id', $studentId)
            ->orderByDesc('session_date')
            ->value('session_date');

        $history = [];
        $completionPercents = [];

        foreach ($sessions as $session) {
            $items = $session->workoutDay?->items ?? collect();
            $totalItems = $items->count();
            $checkedIds = WorkoutItemCheck::where('workout_session_id', $session->id)
                ->where('is_checked', true)
                ->pluck('workout_item_id')
                ->all();

            $done = $totalItems > 0 ? count(array_intersect($items->pluck('id')->all(), $checkedIds)) : 0;
            $percent = $totalItems > 0 ? (int) round(($done / $totalItems) * 100) : 0;

            if ($totalItems > 0) {
                $completionPercents[] = $percent;
            }

            $itemsList = $items->map(function ($item) use ($checkedIds) {
                $checked = in_array($item->id, $checkedIds, true);
                return [
                    'name' => $item->exercise?->name,
                    'reps' => $item->reps,
                    'sets' => $item->sets,
                    'status' => $checked ? 'completed' : 'not_realized',
                ];
            });

            $history[] = [
                'date' => $session->session_date?->toDateString(),
                'workout_title' => $session->workoutDay?->title,
                'done' => $done,
                'total' => $totalItems,
                'percent' => $percent,
                'items' => $itemsList,
            ];
        }

        $avgCompletion = count($completionPercents) > 0
            ? (int) round(array_sum($completionPercents) / count($completionPercents))
            : 0;

        return [
            'treinos_realizados' => $totalCompleted,
            'taxa_conclusao' => $avgCompletion,
            'ultimo_treino' => $lastWorkout ? Carbon::parse($lastWorkout)->toDateString() : null,
            'historico_de_treinos' => $history,
        ];
    }
}
