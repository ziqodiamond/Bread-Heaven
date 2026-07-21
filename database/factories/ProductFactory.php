<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'sku' => $this->faker->unique()->bothify('SKU-####??'),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(10000, 500000),
            'stock' => $this->faker->numberBetween(1, 1000),
            'category_id' => null,
            'status' => 'available',
        ];
    }
}
