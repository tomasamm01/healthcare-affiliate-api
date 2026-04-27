<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_plans(): void
    {
        Plan::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/plans');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_plan(): void
    {
        $coverageDetails = ['consultations' => true, 'medications' => true];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/plans', [
                'name' => 'Premium Plan',
                'code' => 'PREM-001',
                'coverage_details' => $coverageDetails,
                'active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'code', 'coverage_details', 'active']
            ]);

        $this->assertDatabaseHas('plans', [
            'name' => 'Premium Plan',
            'code' => 'PREM-001',
        ]);
    }

    public function test_can_show_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                ]
            ]);
    }

    public function test_can_update_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/plans/{$plan->id}", [
                'name' => 'Updated Plan',
                'code' => 'UPD-001',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Plan',
                    'code' => 'UPD-001',
                ]
            ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
        ]);
    }

    public function test_can_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id,
        ]);
    }
}
