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
        | Carts Table
        |--------------------------------------------------------------------------
        | Menyimpan keranjang belanja user
        |--------------------------------------------------------------------------
        */
        Schema::create('carts', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi User
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Status Cart
            |--------------------------------------------------------------------------
            | active    = cart sedang digunakan
            | converted = cart sudah checkout menjadi order
            | abandoned = cart ditinggalkan user
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'converted',
                'abandoned'
            ])->default('active');

            /*
            |--------------------------------------------------------------------------
            | Informasi Cart
            |--------------------------------------------------------------------------
            */

            // Total item unik dalam cart
            $table->unsignedInteger('total_items')
                ->default(0);

            // Total quantity seluruh item
            $table->unsignedInteger('total_quantity')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Perhitungan Harga
            |--------------------------------------------------------------------------
            */

            // Subtotal sebelum discount
            $table->bigInteger('subtotal')
                ->default(0);

            // Total discount cart
            $table->bigInteger('discount_amount')
                ->default(0);

            // Subtotal setelah discount
            $table->bigInteger('final_subtotal')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Voucher / Coupon
            |--------------------------------------------------------------------------
            */

            // Kode voucher yang dipakai
            $table->string('voucher_code')
                ->nullable();

            // Snapshot voucher
            $table->string('voucher_name')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Expired Cart
            |--------------------------------------------------------------------------
            | Digunakan untuk auto cleanup abandoned cart
            |--------------------------------------------------------------------------
            */

            $table->timestamp('expired_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
