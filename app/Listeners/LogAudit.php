<?php

namespace App\Listeners;

use App\Events\AffiliateUpdated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class LogAudit implements ShouldQueue
{
    public function handle(AffiliateUpdated $event): void
    {
        AuditLog::create([
            'entity' => 'affiliate',
            'entity_id' => $event->affiliate->id,
            'action' => $event->action,
            'old_values' => $event->oldValues,
            'new_values' => $event->getNewValues(),
            'user_id' => Auth::id(),
        ]);
    }
}
