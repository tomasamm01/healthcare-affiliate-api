<?php

namespace App\Exceptions;

use Exception;

class InvalidAffiliateOperationException extends Exception
{
    public function __construct(string $reason)
    {
        parent::__construct($reason, 422);
    }
}
