<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ExerciseStoreRequest;
use App\Http\Requests\Teacher\ExerciseUpdateRequest;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Exercise::class);

        $exercises = Exercise::where('teacher_id', Auth::id())->get();

        return response()->json(['exercises' => $exercises]);
    }

    public function store(ExerciseStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Exercise::class);

        $exercise = Exercise::create(array_merge(
            $request->validated(),
            ['teacher_id' => Auth::id()]
        ));

        return response()->json(['exercise' => $exercise], 201);
    }

    public function show(Exercise $exercise): JsonResponse
    {
        $this->authorize('view', $exercise);

        return response()->json(['exercise' => $exercise]);
    }

    public function update(ExerciseUpdateRequest $request, Exercise $exercise): JsonResponse
    {
        $this->authorize('update', $exercise);

        $exercise->update($request->validated());

        return response()->json(['exercise' => $exercise]);
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        $this->authorize('delete', $exercise);

        $exercise->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
