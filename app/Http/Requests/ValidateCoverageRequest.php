<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCoverageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affiliate_id' => 'required|exists:affiliates,id',
            'service_code' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'affiliate_id.required' => 'The affiliate ID is required.',
            'affiliate_id.exists' => 'The affiliate does not exist.',
            'service_code.required' => 'The service code is required.',
        ];
    }
}
