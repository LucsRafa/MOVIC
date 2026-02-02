<?php

namespace App\Http\Requests\Teacher;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ApproveStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', new Enum(StudentStatus::class)],
        ];
    }
}
