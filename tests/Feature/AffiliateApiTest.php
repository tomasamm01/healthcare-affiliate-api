<?php

namespace Tests\Feature;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_affiliates(): void
    {
        Affiliate::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/affiliates');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_affiliate(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/affiliates', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'dni' => '12345678',
                'status' => AffiliateStatus::ACTIVE->value,
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'dni', 'status', 'plan_id']
            ]);

        $this->assertDatabaseHas('affiliates', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dni' => '12345678',
        ]);
    }

    public function test_can_show_affiliate(): void
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/affiliates/{$affiliate->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $affiliate->id,
                    'first_name' => $affiliate->first_name,
                ]
            ]);
    }

    public function test_can_update_affiliate(): void
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/affiliates/{$affiliate->id}", [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                ]
            ]);

        $this->assertDatabaseHas('affiliates', [
            'id' => $affiliate->id,
            'first_name' => 'Jane',
        ]);
    }

    public function test_can_delete_affiliate(): void
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/affiliates/{$affiliate->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('affiliates', ['id' => $affiliate->id]);
    }

    public function test_can_change_affiliate_status(): void
    {
        $affiliate = Affiliate::factory()->create(['status' => AffiliateStatus::ACTIVE]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/affiliates/{$affiliate->id}/status", [
                'status' => AffiliateStatus::INACTIVE->value,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('affiliates', [
            'id' => $affiliate->id,
            'status' => AffiliateStatus::INACTIVE->value,
        ]);
    }

    public function test_can_get_affiliate_status(): void
    {
        $affiliate = Affiliate::factory()->create(['status' => AffiliateStatus::ACTIVE]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/affiliates/{$affiliate->id}/status");

        $response->assertStatus(200)
            ->assertJson([
                'status' => AffiliateStatus::ACTIVE->value,
            ]);
    }

    public function test_can_add_dependent_to_holder(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/affiliates/{$holder->id}/dependents", [
                'first_name' => 'Child',
                'last_name' => 'Doe',
                'dni' => '87654321',
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('affiliates', [
            'first_name' => 'Child',
            'holder_id' => $holder->id,
        ]);
    }

    public function test_can_get_family_group(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        Affiliate::factory()->for($holder, 'holder')->count(2)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/affiliates/{$holder->id}/family-group");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_remove_dependent(): void
    {
        $holder = Affiliate::factory()->create(['holder_id' => null]);
        $dependent = Affiliate::factory()->for($holder, 'holder')->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/affiliates/{$holder->id}/dependents/{$dependent->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('affiliates', ['id' => $dependent->id]);
    }
}
