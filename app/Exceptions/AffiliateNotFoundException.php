<?php

namespace App\Exceptions;

use Exception;

class AffiliateNotFoundException extends Exception
{
    protected $message = 'Affiliate not found';

    public function __construct(int $affiliateId)
    {
        parent::__construct("Affiliate with ID {$affiliateId} not found", 404);
    }
}
