<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pitch_deck,business_plan,financials,financial,legal,other'],
            'name' => ['nullable', 'string', 'max:255'],
            'startup_id' => ['nullable', 'exists:startups,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}