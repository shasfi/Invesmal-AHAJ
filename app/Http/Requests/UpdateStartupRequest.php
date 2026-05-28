<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStartupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, [User::ROLE_STUDENT_FOUNDER, User::ROLE_ADMIN]);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'industry' => ['sometimes', 'string', 'max:100'],
            'stage' => ['sometimes', 'in:idea,mvp,funded'],
            'website' => ['nullable', 'url', 'max:255'],
            'team_size' => ['nullable', 'integer', 'min:1'],
            'funding_goal' => ['nullable', 'numeric', 'min:0'],
            'equity_offered' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'mission' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'problem' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}