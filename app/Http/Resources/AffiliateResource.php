<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'dni' => $this->dni,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'is_holder' => $this->isHolder(),
            'holder_id' => $this->holder_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
