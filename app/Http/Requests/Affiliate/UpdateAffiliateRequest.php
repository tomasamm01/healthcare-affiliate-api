<?php

namespace App\Http\Requests\Affiliate;

use App\Enums\AffiliateStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAffiliateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'dni' => ['sometimes', 'required', 'string', Rule::unique('affiliates', 'dni')->ignore($this->affiliate)],
            'status' => ['nullable', Rule::enum(AffiliateStatus::class)],
            'plan_id' => 'sometimes|required|exists:plans,id',
            'holder_id' => 'nullable|exists:affiliates,id',
        ];
    }
}
