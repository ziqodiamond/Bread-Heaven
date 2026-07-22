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
        | Flash Sales Table
        |--------------------------------------------------------------------------
        | Menyimpan event flash sale / promo campaign
        |--------------------------------------------------------------------------
        */
        Schema::create('flash_sales', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Flash Sale
            |--------------------------------------------------------------------------
            */

            // Nama flash sale
            // Contoh:
            // Midnight Sale
            // Ramadhan Sale
            // Payday Sale
            $table->string('name');

            // Slug SEO / URL
            $table->string('slug')
                ->unique();

            // Deskripsi flash sale
            $table->longText('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Banner & Thumbnail
            |--------------------------------------------------------------------------
            */

            // Banner utama
            $table->string('banner')
                ->nullable();

            // Thumbnail
            $table->string('thumbnail')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tampilan Promo
            |--------------------------------------------------------------------------
            */

            // Label promo
            // Contoh:
            // FLASH SALE
            // SUPER DEAL
            $table->string('label')
                ->nullable();

            // Warna badge
            // Contoh:
            // red
            // orange
            // yellow
            $table->string('badge_color')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jadwal Flash Sale
            |--------------------------------------------------------------------------
            */

            // Waktu mulai
            $table->timestamp('start_at');

            // Waktu selesai
            $table->timestamp('end_at');

            /*
            |--------------------------------------------------------------------------
            | Status Flash Sale
            |--------------------------------------------------------------------------
            */

            // Draft / publish
            $table->enum('status', [
                'draft',
                'scheduled',
                'active',
                'expired',
                'cancelled',
            ])->default('draft');

            // Aktif/nonaktif
            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Pengaturan Flash Sale
            |--------------------------------------------------------------------------
            */

            // Tampilkan countdown
            $table->boolean('show_countdown')
                ->default(true);

            // Tampilkan di homepage
            $table->boolean('show_in_homepage')
                ->default(true);

            // Prioritas sorting
            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            // Total view flash sale
            $table->unsignedBigInteger('total_views')
                ->default(0);

            // Total transaksi
            $table->unsignedBigInteger('total_orders')
                ->default(0);

            // Total item terjual
            $table->unsignedBigInteger('total_items_sold')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('meta_title')
                ->nullable();

            $table->text('meta_description')
                ->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Flash Sale Items Table
        |--------------------------------------------------------------------------
        | Menyimpan produk yang termasuk dalam flash sale
        |--------------------------------------------------------------------------
        */

        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('flash_sale_id')
                ->constrained('flash_sales')
                ->cascadeOnDelete();

            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Produk saat ditambah ke flash sale
            |--------------------------------------------------------------------------
            */

            // Nama produk snapshot
            $table->string('product_name');

            // SKU produk snapshot
            $table->string('product_sku');

            // URL gambar produk snapshot
            $table->string('product_image_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Harga Flash Sale
            |--------------------------------------------------------------------------
            */

            // Harga normal produk saat ditambah
            $table->bigInteger('original_price');

            // Harga promo flash sale
            $table->bigInteger('sale_price');

            // Tipe diskon
            $table->enum('discount_type', ['percent', 'fixed'])->default('fixed');

            // Nilai diskon
            $table->bigInteger('discount_value');

            // Persentase diskon
            $table->unsignedInteger('discount_percentage')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Stock Flash Sale
            |--------------------------------------------------------------------------
            */

            // Stok terbatas untuk flash sale
            $table->unsignedInteger('stock_limit')->default(0);

            // Jumlah stok terjual
            $table->unsignedInteger('sold_quantity')->default(0);

            // Batas pembelian per user
            $table->unsignedInteger('max_purchase_per_user')->default(10);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            // Item aktif/nonaktif
            $table->boolean('is_active')->default(true);

            /*
            |--------------------------------------------------------------------------
            | Urutan & Analytics
            |--------------------------------------------------------------------------
            */

            // Urutan tampilan
            $table->unsignedInteger('sort_order')->default(0);

            // Label badge opsional
            $table->string('badge_label')->nullable();

            // Total views item
            $table->unsignedBigInteger('total_views')->default(0);

            // Total checkouts
            $table->unsignedBigInteger('total_checkouts')->default(0);

            $table->timestamps();

            $table->unique(['flash_sale_id', 'product_id']);
            $table->index(['flash_sale_id']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
        Schema::dropIfExists('flash_sales');
    }
};
