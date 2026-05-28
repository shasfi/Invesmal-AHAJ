<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:student_founder,investor,mentor,admin',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
            'university' => 'nullable|string|max:255',
        ];
    }
}