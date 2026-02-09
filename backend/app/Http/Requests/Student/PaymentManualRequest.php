<?php

namespace App\Http\Requests\Student;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PaymentManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Student;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'method' => ['required', new Enum(PaymentMethod::class)],
            'receipt_url' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
