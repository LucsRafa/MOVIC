<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSession;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StudentPlanController extends Controller
{
    public function active(): JsonResponse
    {
        $plan = WorkoutPlan::where('student_id', Auth::id())
            ->where('is_active', true)
            ->with(['days.items.exercise'])
            ->first();

        if ($plan) {
            $plan->setRelation(
                'days',
                $plan->days
                    ->sortBy('weekday')
                    ->values()
                    ->map(function ($day) {
                        $latestSession = WorkoutSession::where('student_id', Auth::id())
                            ->where('workout_day_id', $day->id)
                            ->with(['itemChecks' => function ($query) {
                                $query->where('is_checked', true);
                            }])
                            ->orderByDesc('session_date')
                            ->orderByDesc('id')
                            ->first();

                        /** @var Collection<int, mixed> $checksByItemId */
                        $checksByItemId = $latestSession
                            ? $latestSession->itemChecks->keyBy('workout_item_id')
                            : collect();

                        $day->setRelation(
                            'items',
                            $day->items
                                ->sortBy('item_order')
                                ->values()
                                ->map(function ($item) use ($checksByItemId) {
                                    $check = $checksByItemId->get($item->id);
                                    $completedAt = $check?->checked_at?->toIso8601String();

                                    $item->setAttribute('completed_at', $completedAt);
                                    $item->setAttribute('is_checked', (bool) $completedAt);

                                    return $item;
                                })
                        );

                        $day->setAttribute('latest_session_date', $latestSession?->session_date?->toDateString());

                        return $day;
                    })
            );
        }

        return response()->json([
            'status' => 'success',
            'plan' => $plan,
        ]);
    }
}
