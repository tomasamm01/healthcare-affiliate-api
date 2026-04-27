<?php

namespace App\Http\Requests\Affiliate;

use App\Enums\AffiliateStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAffiliateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => 'required|string|unique:affiliates,dni',
            'status' => ['nullable', Rule::enum(AffiliateStatus::class)],
            'plan_id' => 'required|exists:plans,id',
            'holder_id' => [
                'nullable',
                'exists:affiliates,id',
                'different:plan_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.unique' => 'An affiliate with this DNI already exists.',
            'plan_id.exists' => 'The selected plan does not exist.',
            'holder_id.exists' => 'The selected holder does not exist.',
        ];
    }
}
