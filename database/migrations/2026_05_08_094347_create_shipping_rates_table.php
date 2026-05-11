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
        Schema::create('shipping_rates', function (Blueprint $table) {

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Provider Shipping API
            |--------------------------------------------------------------------------
            */

            $table->string('provider');

            /*
            |--------------------------------------------------------------------------
            | Informasi Courier
            |--------------------------------------------------------------------------
            */

            $table->string('courier_name');

            $table->string('courier_code');

            $table->string('service_name');

            $table->string('service_code');

            /*
            |--------------------------------------------------------------------------
            | Informasi Pengiriman
            |--------------------------------------------------------------------------
            */

            $table->integer('weight');

            $table->string('etd')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Harga Ongkir
            |--------------------------------------------------------------------------
            */

            $table->bigInteger('price');

            /*
            |--------------------------------------------------------------------------
            | Snapshot Request
            |--------------------------------------------------------------------------
            */

            $table->json('origin')->nullable();

            $table->json('destination')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Response API
            |--------------------------------------------------------------------------
            */

            $table->json('response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
