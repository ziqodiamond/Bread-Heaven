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
        | Flash Sale Items Table
        |--------------------------------------------------------------------------
        | Menyimpan produk yang masuk flash sale
        |--------------------------------------------------------------------------
        */
        Schema::create('flash_sale_items', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // Relasi flash sale
            $table->foreignUuid('flash_sale_id')
                ->constrained('flash_sales')
                ->cascadeOnDelete();

            // Relasi produk
            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Produk
            |--------------------------------------------------------------------------
            */

            // Nama produk
            $table->string('product_name');

            // SKU produk
            $table->string('product_sku');

            // Thumbnail produk
            $table->string('product_image_url')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Harga Flash Sale
            |--------------------------------------------------------------------------
            */

            // Harga asli produk
            $table->bigInteger('original_price')
                ->default(0);

            // Harga flash sale
            $table->bigInteger('sale_price')
                ->default(0);

            // Tipe discount
            // percent
            // fixed
            $table->enum('discount_type', [
                'percent',
                'fixed',
            ]);

            // Nilai discount
            $table->bigInteger('discount_value')
                ->default(0);

            // Persentase discount
            $table->unsignedInteger('discount_percentage')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Stock Flash Sale
            |--------------------------------------------------------------------------
            */

            // Limit stok flash sale
            $table->unsignedInteger('stock_limit')
                ->default(0);

            // Total stok terjual
            $table->unsignedInteger('sold_quantity')
                ->default(0);

            // Maksimal pembelian per user
            $table->unsignedInteger('max_purchase_per_user')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Status Flash Sale Item
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Informasi Tambahan
            |--------------------------------------------------------------------------
            */

            // Prioritas sorting
            $table->unsignedInteger('sort_order')
                ->default(0);

            // Badge promo custom
            $table->string('badge_label')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            // Total view item
            $table->unsignedBigInteger('total_views')
                ->default(0);

            // Total checkout
            $table->unsignedBigInteger('total_checkouts')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Constraint
            |--------------------------------------------------------------------------
            */

            // Mencegah duplicate produk
            $table->unique([
                'flash_sale_id',
                'product_id',
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
    }
};
