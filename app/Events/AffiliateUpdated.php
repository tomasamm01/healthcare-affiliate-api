<?php

namespace App\Events;

use App\Models\Affiliate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliateUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Affiliate $affiliate,
        public string $action,
        public array $oldValues = []
    ) {}

    public function getNewValues(): array
    {
        return $this->affiliate->getAttributes();
    }
}
