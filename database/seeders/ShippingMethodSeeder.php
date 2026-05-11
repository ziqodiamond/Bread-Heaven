<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingMethods = [

            /*
            |--------------------------------------------------------------------------
            | JNE
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'JNE',
                'courier_code' => 'jne',

                'service_name' => 'REG',
                'service_code' => 'reg',

                'description' => 'Layanan reguler JNE',

                'estimated_delivery' => '2-4 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'JNE',
                'courier_code' => 'jne',

                'service_name' => 'YES',
                'service_code' => 'yes',

                'description' => 'Layanan express JNE',

                'estimated_delivery' => '1 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'JNE',
                'courier_code' => 'jne',

                'service_name' => 'OKE',
                'service_code' => 'oke',

                'description' => 'Layanan ekonomis JNE',

                'estimated_delivery' => '3-6 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | J&T Express
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'J&T Express',
                'courier_code' => 'jnt',

                'service_name' => 'EZ',
                'service_code' => 'ez',

                'description' => 'Layanan reguler J&T',

                'estimated_delivery' => '2-4 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | SiCepat
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'SiCepat',
                'courier_code' => 'sicepat',

                'service_name' => 'REG',
                'service_code' => 'reg',

                'description' => 'Layanan reguler SiCepat',

                'estimated_delivery' => '2-4 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'SiCepat',
                'courier_code' => 'sicepat',

                'service_name' => 'BEST',
                'service_code' => 'best',

                'description' => 'Layanan express SiCepat',

                'estimated_delivery' => '1 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'SiCepat',
                'courier_code' => 'sicepat',

                'service_name' => 'HALU',
                'service_code' => 'halu',

                'description' => 'Layanan hemat SiCepat',

                'estimated_delivery' => '3-6 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | AnterAja
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'AnterAja',
                'courier_code' => 'anteraja',

                'service_name' => 'Regular',
                'service_code' => 'regular',

                'description' => 'Layanan reguler AnterAja',

                'estimated_delivery' => '2-5 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'AnterAja',
                'courier_code' => 'anteraja',

                'service_name' => 'Next Day',
                'service_code' => 'next_day',

                'description' => 'Layanan next day AnterAja',

                'estimated_delivery' => '1 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | Ninja Xpress
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'Ninja Xpress',
                'courier_code' => 'ninja',

                'service_name' => 'Standard',
                'service_code' => 'standard',

                'description' => 'Layanan standar Ninja Xpress',

                'estimated_delivery' => '2-4 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | POS Indonesia
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'POS Indonesia',
                'courier_code' => 'pos',

                'service_name' => 'Paket Kilat Khusus',
                'service_code' => 'kilat_khusus',

                'description' => 'Layanan cepat POS Indonesia',

                'estimated_delivery' => '2-5 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | TIKI
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'TIKI',
                'courier_code' => 'tiki',

                'service_name' => 'REG',
                'service_code' => 'reg',

                'description' => 'Layanan reguler TIKI',

                'estimated_delivery' => '2-5 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            [
                'provider' => 'biteship',

                'courier_name' => 'TIKI',
                'courier_code' => 'tiki',

                'service_name' => 'ONS',
                'service_code' => 'ons',

                'description' => 'Layanan overnight TIKI',

                'estimated_delivery' => '1 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | Lion Parcel
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'Lion Parcel',
                'courier_code' => 'lion',

                'service_name' => 'REGPACK',
                'service_code' => 'regpack',

                'description' => 'Layanan reguler Lion Parcel',

                'estimated_delivery' => '2-5 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | ID Express
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'ID Express',
                'courier_code' => 'idexpress',

                'service_name' => 'Standard',
                'service_code' => 'standard',

                'description' => 'Layanan standar ID Express',

                'estimated_delivery' => '2-4 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],

            /*
            |--------------------------------------------------------------------------
            | SAP Express
            |--------------------------------------------------------------------------
            */

            [
                'provider' => 'biteship',

                'courier_name' => 'SAP Express',
                'courier_code' => 'sap',

                'service_name' => 'REG',
                'service_code' => 'reg',

                'description' => 'Layanan reguler SAP Express',

                'estimated_delivery' => '2-5 Hari',

                'additional_fee' => 0,

                'status' => 'available',
            ],
        ];

        foreach ($shippingMethods as $method) {

            ShippingMethod::create($method);
        }
    }
}
