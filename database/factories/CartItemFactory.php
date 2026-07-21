<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    public function definition(): array
    {
        $originalPrice = $this->faker->numberBetween(10000, 500000);
        
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'product_name' => $this->faker->words(3, true),
            'product_sku' => $this->faker->unique()->bothify('SKU-####??'),
            'quantity' => $this->faker->numberBetween(1, 5),
            'original_price' => $originalPrice,
            'product_price' => $originalPrice,
            'discount_amount' => 0,
            'original_subtotal' => $originalPrice,
            'subtotal' => $originalPrice,
        ];
    }
}
