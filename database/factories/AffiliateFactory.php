<?php

namespace Database\Factories;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliateFactory extends Factory
{
    protected $model = Affiliate::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dni' => fake()->unique()->numerify('########'),
            'status' => fake()->randomElement(AffiliateStatus::cases())->value,
            'plan_id' => Plan::factory(),
            'holder_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AffiliateStatus::ACTIVE->value,
        ]);
    }

    public function holder(): static
    {
        return $this->state(fn (array $attributes) => [
            'holder_id' => null,
        ]);
    }

    public function dependent(Affiliate $holder): static
    {
        return $this->state(fn (array $attributes) => [
            'holder_id' => $holder->id,
            'plan_id' => $holder->plan_id,
            'status' => AffiliateStatus::PENDING->value,
        ]);
    }
}
