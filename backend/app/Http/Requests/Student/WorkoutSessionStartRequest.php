<?php

namespace App\Http\Requests\Student;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class WorkoutSessionStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Student;
    }

    public function rules(): array
    {
        return [
            'workout_day_id' => ['required', 'integer', 'exists:workout_days,id'],
            'session_date' => ['nullable', 'date'],
        ];
    }
}
