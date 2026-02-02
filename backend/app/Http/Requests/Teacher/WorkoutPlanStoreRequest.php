<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class WorkoutPlanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
