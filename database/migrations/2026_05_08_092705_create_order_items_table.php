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
            $table->longText('product_description')->nullable();

            // Thumbnail produk
            $table->string('product_image_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Harga Produk
            |--------------------------------------------------------------------------
            */

            // Harga produk saat checkout
            // Menggunakan bigint
            $table->bigInteger('product_price');

            /*
            |--------------------------------------------------------------------------
            | Informasi Quantity
            |--------------------------------------------------------------------------
            */

            // Jumlah produk dibeli
            $table->unsignedInteger('quantity')->default(1);

            // Berat produk per item
            $table->unsignedInteger('product_weight')->default(0);

            // Total berat
            // quantity × product_weight
            $table->unsignedInteger('total_weight')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Total Harga
            |--------------------------------------------------------------------------
            */

            // Total harga item
            // quantity × product_price
            $table->bigInteger('subtotal')->default(0);

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
