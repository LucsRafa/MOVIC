<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ExerciseStoreRequest;
use App\Http\Requests\Teacher\ExerciseUpdateRequest;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Exercise::class);

        $exercises = Exercise::where('teacher_id', Auth::id())->get();

        return response()->json([
            'status' => 'success',
            'exercises' => $exercises,
        ]);
    }

    public function store(ExerciseStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Exercise::class);

        $data = $request->validated();
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('exercises', 'public');
            $data['video_url'] = Storage::url($path);
        }

        $exercise = Exercise::create(array_merge(
            $data,
            ['teacher_id' => Auth::id()]
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Exercicio criado com sucesso.',
            'exercise' => $exercise,
        ], 201);
    }

    public function show(Exercise $exercise): JsonResponse
    {
        $this->authorize('view', $exercise);

        return response()->json([
            'status' => 'success',
            'exercise' => $exercise,
        ]);
    }

    public function update(ExerciseUpdateRequest $request, Exercise $exercise): JsonResponse
    {
        $this->authorize('update', $exercise);

        $data = $request->validated();
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('exercises', 'public');
            $data['video_url'] = Storage::url($path);
        }

        $exercise->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Exercicio atualizado com sucesso.',
            'exercise' => $exercise,
        ]);
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        $this->authorize('delete', $exercise);

        $exercise->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Exercicio removido com sucesso.',
        ]);
    }
}
