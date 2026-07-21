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

            // Kategori produk (FK to categories)
            $table->unsignedBigInteger('category_id')->nullable()->after('sku');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');

            // Deskripsi produk
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Harga & Stok
            |--------------------------------------------------------------------------
            | Menggunakan bigint agar aman untuk sistem transaksi
            |--------------------------------------------------------------------------
            */

            // Harga asli produk dalam rupiah
            // Contoh:
            // 150000 = Rp150.000
            $table->bigInteger('price');

            /*
            |--------------------------------------------------------------------------
            | Discount Product
            |--------------------------------------------------------------------------
            | Harga promo / harga coret produk
            |--------------------------------------------------------------------------
            */

            // Harga setelah diskon
            // Contoh:
            // price      = 100000
            // sale_price = 75000
            $table->bigInteger('sale_price')
                ->nullable();

            // Jadwal mulai diskon
            $table->timestamp('discount_start_at')
                ->nullable();

            // Jadwal selesai diskon
            $table->timestamp('discount_end_at')
                ->nullable();

            // Label promo
            // Contoh:
            // Flash Sale
            // Promo Ramadhan
            // Diskon Spesial
            $table->string('discount_label')
                ->nullable();

            // Tipe diskon produk
            // percent = diskon persen
            // fixed   = potongan langsung
            $table->enum('discount_type', [
                'percent',
                'fixed',
            ])->nullable();

            // Nilai diskon
            // Contoh:
            // 10 = 10%
            // 5000 = Rp5.000
            $table->decimal('discount_value', 10, 2)
                ->nullable();

            // Maksimal potongan diskon (untuk percent type)
            $table->bigInteger('discount_max')
                ->nullable();

            // Stok produk
            $table->unsignedInteger('stock')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Informasi Shipping
            |--------------------------------------------------------------------------
            | Dibutuhkan untuk cek ongkir Biteship
            |--------------------------------------------------------------------------
            */

            // Berat produk dalam gram
            $table->unsignedInteger('weight')
                ->default(0);

            // Dimensi produk (opsional)
            $table->unsignedInteger('length')
                ->nullable();

            $table->unsignedInteger('width')
                ->nullable();

            $table->unsignedInteger('height')
                ->nullable();

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
