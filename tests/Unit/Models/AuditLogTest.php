<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_for_entity_filters_by_entity(): void
    {
        $affiliateLog = AuditLog::factory()->create(['entity' => 'Affiliate']);
        $planLog = AuditLog::factory()->create(['entity' => 'Plan']);

        $affiliateLogs = AuditLog::forEntity('Affiliate')->get();

        $this->assertCount(1, $affiliateLogs);
        $this->assertTrue($affiliateLogs->contains($affiliateLog));
        $this->assertFalse($affiliateLogs->contains($planLog));
    }

    public function test_scope_for_entity_id_filters_by_entity_id(): void
    {
        $log1 = AuditLog::factory()->create(['entity_id' => 1]);
        $log2 = AuditLog::factory()->create(['entity_id' => 2]);

        $logs = AuditLog::forEntityId(1)->get();

        $this->assertCount(1, $logs);
        $this->assertTrue($logs->contains($log1));
        $this->assertFalse($logs->contains($log2));
    }

    public function test_old_values_is_casted_to_array(): void
    {
        $oldValues = ['status' => 'active', 'plan_id' => 1];
        $log = AuditLog::factory()->create(['old_values' => $oldValues]);

        $this->assertIsArray($log->old_values);
        $this->assertEquals($oldValues, $log->old_values);
    }

    public function test_new_values_is_casted_to_array(): void
    {
        $newValues = ['status' => 'inactive', 'plan_id' => 2];
        $log = AuditLog::factory()->create(['new_values' => $newValues]);

        $this->assertIsArray($log->new_values);
        $this->assertEquals($newValues, $log->new_values);
    }
}
