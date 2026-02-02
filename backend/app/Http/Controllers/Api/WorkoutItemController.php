<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\WorkoutItemStoreRequest;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use Illuminate\Http\JsonResponse;

class WorkoutItemController extends Controller
{
    public function store(WorkoutItemStoreRequest $request, WorkoutDay $day): JsonResponse
    {
        $this->authorize('update', $day);

        $data = $request->validated();

        $item = WorkoutItem::create([
            'workout_day_id' => $day->id,
            'exercise_id' => $data['exercise_id'],
            'item_order' => $data['item_order'] ?? 1,
            'sets' => $data['sets'],
            'reps' => $data['reps'],
            'rest_seconds' => $data['rest_seconds'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json(['item' => $item], 201);
    }
}
