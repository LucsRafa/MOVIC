<?php

namespace App\Http\Requests\Student;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class WorkoutItemCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Student;
    }

    public function rules(): array
    {
        return [
            'workout_item_id' => ['required', 'integer', 'exists:workout_items,id'],
            'is_checked' => ['required', 'boolean'],
        ];
    }
}
