<?php

namespace App\Http\Requests\Teacher;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class TeacherPaymentRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'method' => ['required', 'string', 'in:' . implode(',', [
                PaymentMethod::Card->value,
                PaymentMethod::Pix->value,
            ])],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
