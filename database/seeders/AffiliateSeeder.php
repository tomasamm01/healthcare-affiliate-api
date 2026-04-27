<?php

namespace Database\Seeders;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class AffiliateSeeder extends Seeder
{
    public function run(): void
    {
        $standardPlan = Plan::where('code', 'STD-002')->first();
        $premiumPlan = Plan::where('code', 'PREM-003')->first();
        $familyPlan = Plan::where('code', 'FAM-004')->first();

        // Create holders
        $holder1 = Affiliate::create([
            'first_name' => 'Juan',
            'last_name' => 'García',
            'dni' => '12345678',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $standardPlan->id,
            'holder_id' => null,
        ]);

        $holder2 = Affiliate::create([
            'first_name' => 'María',
            'last_name' => 'Rodríguez',
            'dni' => '87654321',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $premiumPlan->id,
            'holder_id' => null,
        ]);

        $holder3 = Affiliate::create([
            'first_name' => 'Carlos',
            'last_name' => 'López',
            'dni' => '11223344',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $familyPlan->id,
            'holder_id' => null,
        ]);

        // Create dependents for holder1
        Affiliate::create([
            'first_name' => 'Ana',
            'last_name' => 'García',
            'dni' => '12345679',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $standardPlan->id,
            'holder_id' => $holder1->id,
        ]);

        Affiliate::create([
            'first_name' => 'Pedro',
            'last_name' => 'García',
            'dni' => '12345680',
            'status' => AffiliateStatus::PENDING->value,
            'plan_id' => $standardPlan->id,
            'holder_id' => $holder1->id,
        ]);

        // Create dependents for holder2
        Affiliate::create([
            'first_name' => 'Laura',
            'last_name' => 'Rodríguez',
            'dni' => '87654322',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $premiumPlan->id,
            'holder_id' => $holder2->id,
        ]);

        // Create dependents for holder3 (family plan)
        Affiliate::create([
            'first_name' => 'Sofia',
            'last_name' => 'López',
            'dni' => '11223345',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $familyPlan->id,
            'holder_id' => $holder3->id,
        ]);

        Affiliate::create([
            'first_name' => 'Mateo',
            'last_name' => 'López',
            'dni' => '11223346',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $familyPlan->id,
            'holder_id' => $holder3->id,
        ]);

        Affiliate::create([
            'first_name' => 'Valentina',
            'last_name' => 'López',
            'dni' => '11223347',
            'status' => AffiliateStatus::ACTIVE->value,
            'plan_id' => $familyPlan->id,
            'holder_id' => $holder3->id,
        ]);

        // Create some additional standalone affiliates
        Affiliate::create([
            'first_name' => 'Roberto',
            'last_name' => 'Fernández',
            'dni' => '99887766',
            'status' => AffiliateStatus::SUSPENDED->value,
            'plan_id' => $standardPlan->id,
            'holder_id' => null,
        ]);

        Affiliate::create([
            'first_name' => 'Elena',
            'last_name' => 'Martínez',
            'dni' => '55443322',
            'status' => AffiliateStatus::INACTIVE->value,
            'plan_id' => $premiumPlan->id,
            'holder_id' => null,
        ]);
    }
}
