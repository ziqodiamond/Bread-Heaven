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
        | Product Images Table
        |--------------------------------------------------------------------------
        | Menyimpan multiple image produk
        |--------------------------------------------------------------------------
        */
        Schema::create('product_images', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi Product
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Gambar
            |--------------------------------------------------------------------------
            */

            // Path gambar
            $table->string('image_url');

            // Alt text gambar
            $table->string('alt_text')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengurutan
            |--------------------------------------------------------------------------
            */

            // Urutan gambar
            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Thumbnail Utama
            |--------------------------------------------------------------------------
            */

            // Menandakan gambar utama
            $table->boolean('is_primary')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
