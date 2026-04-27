<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'code' => 'BASIC-001',
                'coverage_details' => [
                    'services' => [
                        ['code' => 'CONSULT', 'name' => 'General Consultation', 'requires_authorization' => false],
                        ['code' => 'LAB', 'name' => 'Basic Laboratory Tests', 'requires_authorization' => false],
                    ],
                    'limits' => [
                        'consultations_per_year' => 6,
                        'emergency_coverage' => true,
                    ],
                ],
                'active' => true,
            ],
            [
                'name' => 'Standard Plan',
                'code' => 'STD-002',
                'coverage_details' => [
                    'services' => [
                        ['code' => 'CONSULT', 'name' => 'General Consultation', 'requires_authorization' => false],
                        ['code' => 'LAB', 'name' => 'Laboratory Tests', 'requires_authorization' => false],
                        ['code' => 'XRAY', 'name' => 'X-Ray', 'requires_authorization' => false],
                        ['code' => 'PHYSIO', 'name' => 'Physiotherapy', 'requires_authorization' => false],
                    ],
                    'limits' => [
                        'consultations_per_year' => 12,
                        'emergency_coverage' => true,
                    ],
                ],
                'active' => true,
            ],
            [
                'name' => 'Premium Plan',
                'code' => 'PREM-003',
                'coverage_details' => [
                    'services' => [
                        ['code' => 'CONSULT', 'name' => 'General Consultation', 'requires_authorization' => false],
                        ['code' => 'LAB', 'name' => 'Laboratory Tests', 'requires_authorization' => false],
                        ['code' => 'XRAY', 'name' => 'X-Ray', 'requires_authorization' => false],
                        ['code' => 'SURGERY', 'name' => 'Surgery', 'requires_authorization' => true],
                        ['code' => 'HOSPITAL', 'name' => 'Hospitalization', 'requires_authorization' => true],
                        ['code' => 'PHYSIO', 'name' => 'Physiotherapy', 'requires_authorization' => false],
                        ['code' => 'DENTAL', 'name' => 'Dental Care', 'requires_authorization' => false],
                    ],
                    'limits' => [
                        'consultations_per_year' => 24,
                        'emergency_coverage' => true,
                    ],
                ],
                'active' => true,
            ],
            [
                'name' => 'Family Plan',
                'code' => 'FAM-004',
                'coverage_details' => [
                    'services' => [
                        ['code' => 'CONSULT', 'name' => 'General Consultation', 'requires_authorization' => false],
                        ['code' => 'LAB', 'name' => 'Laboratory Tests', 'requires_authorization' => false],
                        ['code' => 'XRAY', 'name' => 'X-Ray', 'requires_authorization' => false],
                        ['code' => 'PEDIATRIC', 'name' => 'Pediatric Care', 'requires_authorization' => false],
                    ],
                    'limits' => [
                        'consultations_per_year' => 20,
                        'emergency_coverage' => true,
                        'family_discount' => 0.15,
                    ],
                ],
                'active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
