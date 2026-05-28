<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(fn ($query) => $query->where('role', $this->input('role'))),
            ],
            'role' => 'required|in:student_founder,investor,mentor,admin',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'No account found for this email and role.',
        ];
    }
}
