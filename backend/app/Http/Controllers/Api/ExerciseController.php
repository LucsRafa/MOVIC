<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ExerciseStoreRequest;
use App\Http\Requests\Teacher\ExerciseUpdateRequest;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    private function deleteStoredVideo(?string $url): void
    {
        if (!$url) {
            return;
        }

        $appUrl = rtrim(config('app.url'), '/');

        if (str_starts_with($url, $appUrl . '/storage/')) {
            $path = str_replace($appUrl . '/storage/', '', $url);
            Storage::disk('public')->delete($path);
            return;
        }

        if (str_starts_with($url, '/storage/')) {
            $path = ltrim(str_replace('/storage/', '', $url), '/');
            Storage::disk('public')->delete($path);
        }
    }

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

        Log::info('Exercise store request', [
            'user_id' => Auth::id(),
            'origin' => $request->headers->get('origin'),
            'content_type' => $request->header('content-type'),
            'has_video_file' => $request->hasFile('video_file'),
            'has_video_url' => (bool) $request->input('video_url'),
        ]);

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
            'message' => 'Exercício criado com sucesso.',
            'exercise' => $exercise->fresh(),
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
        $currentVideo = $exercise->getRawOriginal('video_url');

        if ($request->boolean('remove_video')) {
            $this->deleteStoredVideo($currentVideo);
            $data['video_url'] = null;
        }

        if ($request->hasFile('video_file')) {
            $this->deleteStoredVideo($currentVideo);
            $path = $request->file('video_file')->store('exercises', 'public');
            $data['video_url'] = Storage::url($path);
        }

        if (array_key_exists('video_url', $data) && $data['video_url'] && $currentVideo !== $data['video_url']) {
            $this->deleteStoredVideo($currentVideo);
        }

        unset($data['remove_video']);

        $exercise->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Exercício atualizado com sucesso.',
            'exercise' => $exercise->fresh(),
        ]);
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        $this->authorize('delete', $exercise);

        $this->deleteStoredVideo($exercise->getRawOriginal('video_url'));
        $exercise->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Exercício removido com sucesso.',
        ]);
    }
}
