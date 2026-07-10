<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\VoucherCombination;
use Illuminate\Database\Seeder;

class VoucherCombinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create sample vouchers
        $discountVoucher = Voucher::first(function ($v) {
            return in_array($v->type, ['fixed', 'percent']);
        }) ?? Voucher::create([
            'name' => 'Diskon 10%',
            'code' => 'DISKON10',
            'type' => 'percent',
            'value' => 10,
            'maximum_discount' => 50000,
            'is_active' => true,
            'is_combinable' => true,
            'combination_type' => 'discount',
        ]);

        $shippingVoucher = Voucher::where('type', 'free_shipping')->first() ?? Voucher::create([
            'name' => 'Gratis Ongkir',
            'code' => 'FREEONGKIR',
            'type' => 'free_shipping',
            'value' => 0,
            'is_active' => true,
            'is_combinable' => true,
            'combination_type' => 'shipping',
        ]);

        // Create combination rule
        VoucherCombination::firstOrCreate([
            'voucher_a_id' => $discountVoucher->id,
            'voucher_b_id' => $shippingVoucher->id,
        ], [
            'is_allowed' => true,
            'rule_description' => 'Diskon dapat dikombinasikan dengan gratis ongkir',
        ]);

        $this->command->info('✓ Voucher combination seeded successfully!');
    }
}
