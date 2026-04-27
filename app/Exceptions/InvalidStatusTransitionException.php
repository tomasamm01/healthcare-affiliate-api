<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(string $currentStatus, string $targetStatus, string $reason = '')
    {
        $message = "Cannot transition from {$currentStatus} to {$targetStatus}";
        if ($reason) {
            $message .= ": {$reason}";
        }
        parent::__construct($message, 422);
    }
}
