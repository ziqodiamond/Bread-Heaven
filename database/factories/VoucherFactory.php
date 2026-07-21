<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->unique()->bothify('VOUCHER-????')),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['fixed', 'percent', 'free_shipping']),
            'value' => $this->faker->numberBetween(5000, 50000),
            'maximum_discount' => $this->faker->numberBetween(50000, 200000),
            'minimum_purchase' => $this->faker->numberBetween(10000, 100000),
            'quota' => $this->faker->numberBetween(10, 1000),
            'used_count' => 0,
            'max_usage_per_user' => $this->faker->numberBetween(1, 5),
            'status' => 'active',
            'is_active' => true,
            'start_at' => now()->subDay(),
            'end_at' => now()->addMonth(),
            'is_stackable' => $this->faker->boolean(),
            'members_only' => $this->faker->boolean(),
            'allow_on_flash_sale' => false,
            'allow_on_discount' => false,
            'is_combinable' => true,
            'total_views' => 0,
            'total_claims' => 0,
        ];
    }
}
