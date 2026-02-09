<?php

namespace App\Http\Requests\Teacher;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class TeacherStudentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:' . implode(',', [
                StudentStatus::Active->value,
                StudentStatus::PendingPayment->value,
                StudentStatus::Inactive->value,
                StudentStatus::Trial->value,
            ])],
        ];
    }
}
