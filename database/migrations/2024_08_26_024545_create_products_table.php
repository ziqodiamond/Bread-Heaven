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
        | Products Table
        |--------------------------------------------------------------------------
        | Menyimpan data produk toko
        |--------------------------------------------------------------------------
        */
        Schema::create('products', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Produk
            |--------------------------------------------------------------------------
            */

            // Nama produk
            $table->string('name');

            // Slug SEO friendly
            $table->string('slug')->unique();

            // SKU produk
            $table->string('sku')->unique();

            // Kategori produk
            $table->string('category');

            // Deskripsi produk
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Harga & Stok
            |--------------------------------------------------------------------------
            | Menggunakan bigint agar aman untuk sistem transaksi
            |--------------------------------------------------------------------------
            */

            // Harga produk dalam rupiah
            // Contoh:
            // 150000 = Rp150.000
            $table->bigInteger('price');

            // Stok produk
            $table->unsignedInteger('stock')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Informasi Shipping
            |--------------------------------------------------------------------------
            | Dibutuhkan untuk cek ongkir Biteship/RajaOngkir
            |--------------------------------------------------------------------------
            */

            // Berat produk dalam gram
            $table->unsignedInteger('weight')->default(0);

            // Dimensi produk (opsional)
            $table->unsignedInteger('length')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            /*
            |--------------------------------------------------------------------------
            | discount
            |--------------------------------------------------------------------------
            */
            $table->bigInteger('sale_price')->nullable();

            $table->timestamp('discount_start_at')->nullable();
            $table->timestamp('discount_end_at')->nullable();

            $table->string('discount_label')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Produk
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'available',
                'not_available'
            ])->default('available');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
