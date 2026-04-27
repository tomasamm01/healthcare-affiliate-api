<?php

namespace App\Services;

use App\Enums\AffiliateStatus;
use App\Events\AffiliateUpdated;
use App\Exceptions\InvalidAffiliateOperationException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Affiliate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AffiliateService
{
    public function create(array $data): Affiliate
    {
        $affiliate = Affiliate::create($data);

        event(new AffiliateUpdated($affiliate, 'created', []));

        return $affiliate->load('plan');
    }

    public function update(Affiliate $affiliate, array $data): Affiliate
    {
        $old = $affiliate->getOriginal();

        $affiliate->update($data);

        event(new AffiliateUpdated($affiliate, 'updated', $old));

        return $affiliate->load('plan');
    }

    public function changeStatus(Affiliate $affiliate, AffiliateStatus|string $status): Affiliate
    {
        if (is_string($status)) {
            $status = AffiliateStatus::from($status);
        }

        if (!$affiliate->status->canTransitionTo($status)) {
            throw new InvalidStatusTransitionException(
                $affiliate->status->value,
                $status->value,
                'Invalid status transition for this affiliate'
            );
        }

        $old = $affiliate->getOriginal();

        $affiliate->update(['status' => $status->value]);

        event(new AffiliateUpdated($affiliate, 'status_changed', $old));

        return $affiliate->load('plan');
    }

    public function addDependent(Affiliate $holder, array $dependentData): Affiliate
    {
        return DB::transaction(function () use ($holder, $dependentData) {
            if (!$holder->isHolder()) {
                throw new InvalidAffiliateOperationException('Only holders can have dependents');
            }

            $dependentData['holder_id'] = $holder->id;
            $dependentData['plan_id'] = $holder->plan_id;
            $dependentData['status'] = AffiliateStatus::PENDING->value;

            $dependent = $this->create($dependentData);

            return $dependent;
        });
    }

    public function removeDependent(Affiliate $dependent): void
    {
        DB::transaction(function () use ($dependent) {
            if ($dependent->isHolder()) {
                throw new InvalidAffiliateOperationException('Cannot remove a holder as a dependent');
            }

            $old = $dependent->getOriginal();

            $dependent->delete();

            event(new AffiliateUpdated($dependent, 'deleted', $old));
        });
    }

    public function getFamilyGroup(Affiliate $holder): Collection
    {
        if (!$holder->isHolder()) {
            throw new InvalidAffiliateOperationException('Only holders have family groups');
        }

        return Affiliate::where('holder_id', $holder->id)
            ->orWhere('id', $holder->id)
            ->with('plan')
            ->get();
    }
}
