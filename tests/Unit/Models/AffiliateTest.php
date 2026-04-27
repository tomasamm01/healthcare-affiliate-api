<?php

namespace Tests\Unit\Models;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliate_belongs_to_plan(): void
    {
        $plan = Plan::factory()->create();
        $affiliate = Affiliate::factory()->for($plan)->create();

        $this->assertInstanceOf(Plan::class, $affiliate->plan);
        $this->assertEquals($plan->id, $affiliate->plan->id);
    }

    public function test_affiliate_has_many_dependents(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent1 = Affiliate::factory()->for($holder, 'holder')->create();
        $dependent2 = Affiliate::factory()->for($holder, 'holder')->create();

        $this->assertCount(2, $holder->dependents);
        $this->assertTrue($holder->dependents->contains($dependent1));
        $this->assertTrue($holder->dependents->contains($dependent2));
    }

    public function test_affiliate_belongs_to_holder(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent = Affiliate::factory()->for($holder, 'holder')->create();

        $this->assertInstanceOf(Affiliate::class, $dependent->holder);
        $this->assertEquals($holder->id, $dependent->holder->id);
    }

    public function test_is_holder_returns_true_for_holder(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);

        $this->assertTrue($holder->isHolder());
    }

    public function test_is_holder_returns_false_for_dependent(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent = Affiliate::factory()->for($holder, 'holder')->create();

        $this->assertFalse($dependent->isHolder());
    }

    public function test_scope_active_filters_by_active_status(): void
    {
        $activeAffiliate = Affiliate::factory()->create(['status' => AffiliateStatus::ACTIVE]);
        $inactiveAffiliate = Affiliate::factory()->create(['status' => AffiliateStatus::INACTIVE]);

        $activeAffiliates = Affiliate::active()->get();

        $this->assertCount(1, $activeAffiliates);
        $this->assertTrue($activeAffiliates->contains($activeAffiliate));
        $this->assertFalse($activeAffiliates->contains($inactiveAffiliate));
    }

    public function test_scope_holders_filters_by_null_holder_id(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent = Affiliate::factory()->for($holder, 'holder')->create();

        $holders = Affiliate::holders()->get();

        $this->assertCount(1, $holders);
        $this->assertTrue($holders->contains($holder));
        $this->assertFalse($holders->contains($dependent));
    }

    public function test_scope_dependents_filters_by_not_null_holder_id(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent = Affiliate::factory()->for($holder, 'holder')->create();

        $dependents = Affiliate::dependents()->get();

        $this->assertCount(1, $dependents);
        $this->assertTrue($dependents->contains($dependent));
        $this->assertFalse($dependents->contains($holder));
    }

    public function test_status_is_casted_to_enum(): void
    {
        $affiliate = Affiliate::factory()->create(['status' => AffiliateStatus::ACTIVE]);

        $this->assertInstanceOf(AffiliateStatus::class, $affiliate->status);
        $this->assertEquals(AffiliateStatus::ACTIVE, $affiliate->status);
    }

    public function test_affiliate_uses_soft_deletes(): void
    {
        $affiliate = Affiliate::factory()->create();

        $affiliate->delete();

        $this->assertSoftDeleted('affiliates', ['id' => $affiliate->id]);
        $this->assertNull(Affiliate::find($affiliate->id));
        $this->assertNotNull(Affiliate::withTrashed()->find($affiliate->id));
    }
}
