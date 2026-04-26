<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class ExerciseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048', 'exclude_with:video_file'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo', 'max:512000'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('ExerciseStore validation failed', [
            'user_id' => $this->user()?->id,
            'errors' => $validator->errors()->toArray(),
            'has_video_file' => $this->hasFile('video_file'),
            'content_length' => $this->server('CONTENT_LENGTH'),
            'input_keys' => array_keys($this->all()),
        ]);

        parent::failedValidation($validator);
    }
}
