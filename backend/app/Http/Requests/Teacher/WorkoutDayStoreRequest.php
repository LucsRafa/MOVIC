<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class WorkoutDayStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
