<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:plans,code',
            'coverage_details' => 'nullable|array',
            'coverage_details.services' => 'nullable|array',
            'active' => 'sometimes|boolean',
        ];
    }
}
