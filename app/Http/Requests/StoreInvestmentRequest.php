<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, [User::ROLE_INVESTOR, User::ROLE_ADMIN]);
    }

    public function rules(): array
    {
        return [
            'startup_id' => ['required', 'exists:startups,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'equity_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}