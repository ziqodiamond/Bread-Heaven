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
        | Cart Items Table
        |--------------------------------------------------------------------------
        | Menyimpan item produk di dalam cart
        |--------------------------------------------------------------------------
        */
        Schema::create('cart_items', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // Relasi ke cart
            $table->foreignUuid('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            // Relasi ke produk
            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Quantity & Subtotal
            |--------------------------------------------------------------------------
            */

            // Jumlah produk
            $table->unsignedInteger('quantity')->default(1);



            /*
            |--------------------------------------------------------------------------
            | Unique Constraint
            |--------------------------------------------------------------------------
            | Mencegah produk duplicate dalam satu cart
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'cart_id',
                'product_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
