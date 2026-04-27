<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Basic', 'Standard', 'Premium', 'Family', 'Gold']) . ' Plan',
            'code' => strtoupper(fake()->unique()->lexify('PLAN-????')),
            'coverage_details' => [
                'services' => [
                    ['code' => 'CONSULT', 'name' => 'General Consultation', 'requires_authorization' => false],
                    ['code' => 'LAB', 'name' => 'Laboratory Tests', 'requires_authorization' => false],
                    ['code' => 'XRAY', 'name' => 'X-Ray', 'requires_authorization' => false],
                    ['code' => 'SURGERY', 'name' => 'Surgery', 'requires_authorization' => true],
                    ['code' => 'HOSPITAL', 'name' => 'Hospitalization', 'requires_authorization' => true],
                ],
                'limits' => [
                    'consultations_per_year' => 12,
                    'emergency_coverage' => true,
                ],
            ],
            'active' => true,
        ];
    }
}
