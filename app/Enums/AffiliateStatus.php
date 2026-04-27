<?php

namespace App\Enums;

enum AffiliateStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::INACTIVE => 'Inactive',
        };
    }

    public function canValidateCoverage(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::PENDING => $status === self::ACTIVE || $status === self::INACTIVE,
            self::ACTIVE => $status === self::SUSPENDED || $status === self::INACTIVE,
            self::SUSPENDED => $status === self::ACTIVE || $status === self::INACTIVE,
            self::INACTIVE => false,
        };
    }
}
