<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [

            /*
            |--------------------------------------------------------------------------
            | QRIS
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'QRIS',
                'code' => 'qris',
                'category' => 'qris',

                'provider' => 'midtrans',

                // Fee 0.7%
                'fee_type' => 'percent',
                'fee_value' => 0.7,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | E-Wallet
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'GoPay',
                'code' => 'gopay',
                'category' => 'e_wallet',

                'provider' => 'midtrans',

                // Fee 2%
                'fee_type' => 'percent',
                'fee_value' => 2,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            [
                'name' => 'ShopeePay',
                'code' => 'shopeepay',
                'category' => 'e_wallet',

                'provider' => 'midtrans',

                // Fee 2%
                'fee_type' => 'percent',
                'fee_value' => 2,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            [
                'name' => 'DANA',
                'code' => 'dana',
                'category' => 'e_wallet',

                'provider' => 'midtrans',

                // Fee 2%
                'fee_type' => 'percent',
                'fee_value' => 2,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | Virtual Account
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'BCA Virtual Account',
                'code' => 'bca_va',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                // Rp4.000 sebelum pajak
                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'BNI Virtual Account',
                'code' => 'bni_va',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'BRI Virtual Account',
                'code' => 'bri_va',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'Permata Virtual Account',
                'code' => 'permata_va',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'CIMB Virtual Account',
                'code' => 'cimb_va',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'Mandiri Bill Payment',
                'code' => 'mandiri_bill',
                'category' => 'bank_transfer',

                'provider' => 'midtrans',

                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | Retail Outlet
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Indomaret',
                'code' => 'indomaret',
                'category' => 'retail_outlet',

                'provider' => 'midtrans',

                // Rp3.500 sebelum pajak
                'fee_type' => 'fixed',
                'fee_value' => 3500,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            [
                'name' => 'Alfamart',
                'code' => 'alfamart',
                'category' => 'retail_outlet',

                'provider' => 'midtrans',

                // Rp4.000 sebelum pajak
                'fee_type' => 'fixed',
                'fee_value' => 4000,
                'fee_tax_type' => 'before_tax',

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | Credit Card
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Credit Card',
                'code' => 'credit_card',
                'category' => 'credit_card',

                'provider' => 'midtrans',

                // 2.9%
                'fee_type' => 'percent',
                'fee_value' => 2.9,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | PayLater
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Akulaku',
                'code' => 'akulaku',
                'category' => 'paylater',

                'provider' => 'midtrans',

                // 3%
                'fee_type' => 'percent',
                'fee_value' => 3,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],

            [
                'name' => 'Kredivo',
                'code' => 'kredivo',
                'category' => 'paylater',

                'provider' => 'midtrans',

                // 3%
                'fee_type' => 'percent',
                'fee_value' => 3,
                'fee_tax_type' => 'after_tax',

                'status' => 'available',
            ],
        ];

        foreach ($paymentMethods as $paymentMethod) {

            PaymentMethod::updateOrCreate(

                [
                    'code' => $paymentMethod['code'],
                ],

                $paymentMethod
            );
        }
    }
}
