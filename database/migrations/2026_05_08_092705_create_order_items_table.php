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
        | Order Items Table
        |--------------------------------------------------------------------------
        | Menyimpan detail item produk dalam order
        |--------------------------------------------------------------------------
        */
        Schema::create('order_items', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // Relasi ke order
            $table->foreignUuid('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Relasi ke produk
            $table->foreignUuid('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Produk
            |--------------------------------------------------------------------------
            | Disimpan agar histori order tetap aman
            | walaupun data produk berubah
            |--------------------------------------------------------------------------
            */

            // Nama produk saat checkout
            $table->string('product_name');

            // Slug produk
            $table->string('product_slug');

            // SKU produk
            $table->string('product_sku');

            // Deskripsi produk saat checkout
            $table->longText('product_description')
                ->nullable();

            // Thumbnail produk
            $table->string('product_image_url')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Harga Produk
            |--------------------------------------------------------------------------
            */

            // Harga asli produk
            $table->bigInteger('original_price')
                ->default(0);

            // Harga final produk saat checkout
            $table->bigInteger('product_price')
                ->default(0);

            // Jumlah discount per item
            $table->bigInteger('discount_amount')
                ->default(0);

            // Persentase discount
            $table->unsignedInteger('discount_percentage')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Informasi Discount
            |--------------------------------------------------------------------------
            */

            // Label promo
            // Flash Sale
            // Promo Ramadhan
            $table->string('discount_label')
                ->nullable();

            // Sumber discount
            // product_discount
            // flash_sale
            // voucher
            $table->string('discount_source')
                ->nullable();

            // IDs voucher yang applicable ke produk ini (JSON array)
            $table->json('voucher_ids')
                ->nullable()
                ->default(null);

            // Total discount dari voucher untuk item ini
            $table->bigInteger('voucher_discount_amount')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Informasi Quantity
            |--------------------------------------------------------------------------
            */

            // Jumlah produk dibeli
            $table->unsignedInteger('quantity')
                ->default(1);

            // Berat produk per item
            $table->unsignedInteger('product_weight')
                ->default(0);

            // Total berat
            // quantity × product_weight
            $table->unsignedInteger('total_weight')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Total Harga
            |--------------------------------------------------------------------------
            */

            // Subtotal sebelum discount
            $table->bigInteger('original_subtotal')
                ->default(0);

            // Total harga item setelah discount
            // quantity × product_price
            $table->bigInteger('subtotal')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status Item
            |--------------------------------------------------------------------------
            | Berguna untuk refund/return di masa depan
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'active',
                'cancelled',
                'refunded'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
