<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix tipe data di voucher_shipping_methods: bigint → uuid (PostgreSQL)
        DB::statement('ALTER TABLE voucher_shipping_methods ALTER COLUMN shipping_method_id TYPE uuid USING shipping_method_id::text::uuid');

        // Fix tipe data di voucher_payment_methods: bigint → uuid (PostgreSQL)
        DB::statement('ALTER TABLE voucher_payment_methods ALTER COLUMN payment_method_id TYPE uuid USING payment_method_id::text::uuid');
    }

    public function down(): void
    {
        // Revert back to bigint (PostgreSQL)
        DB::statement('ALTER TABLE voucher_shipping_methods ALTER COLUMN shipping_method_id TYPE bigint USING shipping_method_id::text::bigint');
        DB::statement('ALTER TABLE voucher_payment_methods ALTER COLUMN payment_method_id TYPE bigint USING payment_method_id::text::bigint');
    }
};


