<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class InviteStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
