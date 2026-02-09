<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\WorkoutDayStoreRequest;
use App\Models\WorkoutDay;
use App\Models\WorkoutPlan;
use Illuminate\Http\JsonResponse;

class WorkoutDayController extends Controller
{
    public function store(WorkoutDayStoreRequest $request, WorkoutPlan $plan): JsonResponse
    {
        $this->authorize('update', $plan);

        $day = WorkoutDay::create([
            'workout_plan_id' => $plan->id,
            'weekday' => $request->validated()['weekday'],
            'title' => $request->validated()['title'],
            'notes' => $request->validated()['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Dia de treino criado com sucesso.',
            'day' => $day,
        ], 201);
    }
}
