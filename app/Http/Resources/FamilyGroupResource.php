<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'holder' => new AffiliateResource($this->resource),
            'dependents' => AffiliateResource::collection($this->resource->dependents),
            'total_members' => 1 + $this->resource->dependents->count(),
        ];
    }
}
