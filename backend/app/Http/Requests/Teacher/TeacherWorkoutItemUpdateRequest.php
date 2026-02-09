<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class TeacherWorkoutItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'item_order' => ['nullable', 'integer', 'min:1'],
            'sets' => ['nullable', 'integer', 'min:1'],
            'reps' => ['nullable', 'string', 'max:50'],
            'rest_seconds' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
