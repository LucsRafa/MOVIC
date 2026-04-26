<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Perfil atualizado com sucesso.',
            'user' => $user,
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->validated()['current_password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'error' => 'validation_error',
                'message' => 'Senha atual incorreta.',
                'errors' => [
                    'current_password' => ['Senha atual incorreta.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Senha atualizada com sucesso.',
        ]);
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $path = $request->file('avatar')->store('avatars', 'public');
        $url = Storage::disk('public')->url($path);

        $user->update(['avatar_url' => $url]);

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar atualizado com sucesso.',
            'avatar_url' => $url,
        ]);
    }
}
