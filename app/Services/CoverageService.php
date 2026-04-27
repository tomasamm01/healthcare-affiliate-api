<?php

namespace App\Services;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;

class CoverageService
{
    public function validateCoverage(int $affiliateId, string $serviceCode): array
    {
        $affiliate = Affiliate::with('plan')->findOrFail($affiliateId);

        // Check if affiliate is active
        if (!$affiliate->status->canValidateCoverage()) {
            return [
                'valid' => false,
                'reason' => 'Affiliate is not active',
                'affiliate_status' => $affiliate->status->value,
            ];
        }

        // Check if plan is active
        if (!$affiliate->plan->active) {
            return [
                'valid' => false,
                'reason' => 'Plan is not active',
            ];
        }

        // Check if service is covered
        $coverage = $affiliate->plan->coverage_details;

        if (!$coverage || empty($coverage['services'])) {
            return [
                'valid' => false,
                'reason' => 'Plan has no coverage details',
            ];
        }

        $coveredServices = $coverage['services'];
        $serviceDetails = collect($coveredServices)->firstWhere('code', $serviceCode);

        if (!$serviceDetails) {
            return [
                'valid' => false,
                'reason' => 'Service not covered by plan',
                'available_services' => collect($coveredServices)->pluck('code')->toArray(),
            ];
        }

        // Check service-specific restrictions
        if (isset($serviceDetails['requires_authorization']) && $serviceDetails['requires_authorization']) {
            return [
                'valid' => false,
                'reason' => 'Service requires prior authorization',
                'requires_authorization' => true,
            ];
        }

        // Check limits if applicable
        if (isset($serviceDetails['limit'])) {
            // Here you could implement logic to check usage limits
            // For now, we'll just return the limit information
        }

        return [
            'valid' => true,
            'plan' => [
                'id' => $affiliate->plan->id,
                'name' => $affiliate->plan->name,
                'code' => $affiliate->plan->code,
            ],
            'affiliate' => [
                'id' => $affiliate->id,
                'full_name' => "{$affiliate->first_name} {$affiliate->last_name}",
                'dni' => $affiliate->dni,
            ],
            'service' => $serviceDetails,
        ];
    }

    public function getAffiliateCoverage(int $affiliateId): array
    {
        $affiliate = Affiliate::with('plan')->findOrFail($affiliateId);

        return [
            'affiliate' => [
                'id' => $affiliate->id,
                'full_name' => "{$affiliate->first_name} {$affiliate->last_name}",
                'dni' => $affiliate->dni,
                'status' => $affiliate->status->value,
            ],
            'plan' => [
                'id' => $affiliate->plan->id,
                'name' => $affiliate->plan->name,
                'code' => $affiliate->plan->code,
                'active' => $affiliate->plan->active,
            ],
            'coverage_details' => $affiliate->plan->coverage_details ?? [],
            'can_validate_coverage' => $affiliate->status->canValidateCoverage() && $affiliate->plan->active,
        ];
    }
}
