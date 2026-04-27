<?php

namespace Tests\Unit\Models;

use App\Models\Affiliate;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_has_many_affiliates(): void
    {
        $plan = Plan::factory()->create();
        $affiliate1 = Affiliate::factory()->for($plan)->create();
        $affiliate2 = Affiliate::factory()->for($plan)->create();

        $this->assertCount(2, $plan->affiliates);
        $this->assertTrue($plan->affiliates->contains($affiliate1));
        $this->assertTrue($plan->affiliates->contains($affiliate2));
    }

    public function test_scope_active_filters_by_active_true(): void
    {
        $activePlan = Plan::factory()->create(['active' => true]);
        $inactivePlan = Plan::factory()->create(['active' => false]);

        $activePlans = Plan::active()->get();

        $this->assertCount(1, $activePlans);
        $this->assertTrue($activePlans->contains($activePlan));
        $this->assertFalse($activePlans->contains($inactivePlan));
    }

    public function test_coverage_details_is_casted_to_array(): void
    {
        $coverageDetails = ['consultations' => true, 'medications' => false];
        $plan = Plan::factory()->create(['coverage_details' => $coverageDetails]);

        $this->assertIsArray($plan->coverage_details);
        $this->assertEquals($coverageDetails, $plan->coverage_details);
    }

    public function test_active_is_casted_to_boolean(): void
    {
        $plan = Plan::factory()->create(['active' => true]);

        $this->assertIsBool($plan->active);
        $this->assertTrue($plan->active);
    }
}
