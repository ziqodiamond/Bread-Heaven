<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'active',
            'total_items' => 0,
            'total_quantity' => 0,
            'subtotal' => 0,
            'discount_amount' => 0,
            'final_subtotal' => 0,
            'total_discount_amount' => 0,
            'total_shipping_discount' => 0,
            'vouchers' => [],
        ];
    }
}
