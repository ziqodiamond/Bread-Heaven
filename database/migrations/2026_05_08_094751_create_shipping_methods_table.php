<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Shipping Methods Table
        |--------------------------------------------------------------------------
        | Menyimpan daftar metode pengiriman
        | Contoh:
        | - JNE REG
        | - JNE YES
        | - J&T EZ
        | - SiCepat BEST
        |--------------------------------------------------------------------------
        */
        Schema::create('shipping_methods', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Provider Shipping
            |--------------------------------------------------------------------------
            */

            // Provider shipping API
            // Contoh:
            // biteship
            // rajaongkir
            // manual
            $table->string('provider');

            /*
            |--------------------------------------------------------------------------
            | Informasi Courier
            |--------------------------------------------------------------------------
            */

            // Nama courier
            // Contoh:
            // jne
            // jnt
            // sicepat
            $table->string('courier_name');

            // Code courier
            $table->string('courier_code');

            /*
            |--------------------------------------------------------------------------
            | Informasi Service
            |--------------------------------------------------------------------------
            */

            // Nama service
            // Contoh:
            // REG
            // YES
            // BEST
            $table->string('service_name');

            // Code service
            $table->string('service_code');

            /*
            |--------------------------------------------------------------------------
            | Deskripsi Service
            |--------------------------------------------------------------------------
            */

            // Contoh:
            // Reguler 2-3 hari
            $table->string('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estimasi Pengiriman
            |--------------------------------------------------------------------------
            */

            // Contoh:
            // 2-3 days
            $table->string('estimated_delivery')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Biaya Tambahan
            |--------------------------------------------------------------------------
            */

            // Fee tambahan shipping
            // Contoh:
            // 2000 = Rp2.000
            $table->bigInteger('additional_fee')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'available',
                'unavailable'
            ])->default('available');

            /*
            |--------------------------------------------------------------------------
            | Metadata Tambahan
            |--------------------------------------------------------------------------
            */

            // Menyimpan config tambahan
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
