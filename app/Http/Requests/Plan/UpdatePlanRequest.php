<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => ['sometimes', 'required', 'string', Rule::unique('plans', 'code')->ignore($this->plan)],
            'coverage_details' => 'nullable|array',
            'coverage_details.services' => 'nullable|array',
            'active' => 'sometimes|boolean',
        ];
    }
}
