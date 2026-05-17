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
            | Snapshot Produk
            |--------------------------------------------------------------------------
            | Snapshot ringan untuk optimasi cart
            |--------------------------------------------------------------------------
            */

            // Nama produk saat dimasukkan ke cart
            $table->string('product_name')
                ->nullable();

            // SKU produk
            $table->string('product_sku')
                ->nullable();

            // Thumbnail produk
            $table->string('product_image_url')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Harga
            |--------------------------------------------------------------------------
            */

            // Harga asli produk
            $table->bigInteger('original_price')
                ->default(0);

            // Harga final produk setelah discount
            $table->bigInteger('product_price')
                ->default(0);

            // Jumlah discount per item
            $table->bigInteger('discount_amount')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Quantity & Subtotal
            |--------------------------------------------------------------------------
            */

            // Jumlah produk
            $table->unsignedInteger('quantity')
                ->default(1);

            // Subtotal sebelum discount
            $table->bigInteger('original_subtotal')
                ->default(0);

            // Subtotal final setelah discount
            $table->bigInteger('subtotal')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Discount Metadata
            |--------------------------------------------------------------------------
            */

            // Snapshot label discount
            $table->string('discount_label')
                ->nullable();

            // Snapshot persentase discount
            $table->unsignedInteger('discount_percentage')
                ->default(0);

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
