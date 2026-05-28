<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(fn ($query) => $query->where('role', $this->input('role'))),
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student_founder,investor,mentor',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered for this role. Choose another role or sign in.',
        ];
    }
}
