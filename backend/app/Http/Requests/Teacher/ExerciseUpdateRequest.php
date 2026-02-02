<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class ExerciseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'video_url' => ['sometimes', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
