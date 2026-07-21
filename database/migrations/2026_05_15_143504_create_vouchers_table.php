<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Vouchers Table
        |--------------------------------------------------------------------------
        | Menyimpan voucher / coupon ecommerce
        |--------------------------------------------------------------------------
        */
        Schema::create('vouchers', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Voucher
            |--------------------------------------------------------------------------
            */

            // Nama voucher
            $table->string('name');

            // Kode voucher
            $table->string('code')->unique();

            // Deskripsi voucher
            $table->longText('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tipe Voucher
            |--------------------------------------------------------------------------
            */

            // fixed = potongan langsung, percent = diskon persen, free_shipping = gratis ongkir
            $table->enum('type', ['fixed', 'percent', 'free_shipping']);

            /*
            |--------------------------------------------------------------------------
            | Nilai Voucher
            |--------------------------------------------------------------------------
            */

            // Nilai voucher
            $table->bigInteger('value')->default(0);

            // Maksimal potongan (untuk voucher persen)
            $table->bigInteger('maximum_discount')->nullable();

            // Minimal belanja
            $table->bigInteger('minimum_purchase')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Limit Voucher
            |--------------------------------------------------------------------------
            */

            // Total quota voucher
            $table->unsignedInteger('quota')->nullable();

            // Total voucher terpakai
            $table->unsignedInteger('used_count')->default(0);

            // Limit penggunaan per user
            $table->unsignedInteger('max_usage_per_user')->default(1);

            /*
            |--------------------------------------------------------------------------
            | Status Voucher
            |--------------------------------------------------------------------------
            */

            // Draft / publish
            $table->enum('status', ['draft', 'active', 'expired', 'disabled'])->default('draft');

            // Aktif/nonaktif
            $table->boolean('is_active')->default(true);

            /*
            |--------------------------------------------------------------------------
            | Jadwal Voucher
            |--------------------------------------------------------------------------
            */

            // Waktu mulai voucher
            $table->timestamp('start_at')->nullable();

            // Waktu selesai voucher
            $table->timestamp('end_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengaturan Voucher
            |--------------------------------------------------------------------------
            */

            // Bisa digabung discount lain
            $table->boolean('is_stackable')->default(false);

            // Hanya untuk user login
            $table->boolean('members_only')->default(false);

            // Bisa digunakan di flash sale
            $table->boolean('allow_on_flash_sale')->default(true);

            // Bisa digunakan pada produk dengan discount
            $table->boolean('allow_on_discount')->default(true);

            /*
            |--------------------------------------------------------------------------
            | Kombinasi Voucher
            |--------------------------------------------------------------------------
            */

            // Bisa dikombinasikan dengan voucher lain
            $table->boolean('is_combinable')->default(true);

            // Tipe kombinasi (shipping vs discount)
            $table->string('combination_type')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tampilan Voucher
            |--------------------------------------------------------------------------
            */

            // Label promo
            $table->string('label')->nullable();

            // Warna badge
            $table->string('badge_color')->nullable();

            // Image untuk voucher card
            $table->string('image_path')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            // Total views voucher
            $table->unsignedBigInteger('total_views')->default(0);

            // Total claim voucher
            $table->unsignedBigInteger('total_claims')->default(0);

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('meta_title')->nullable();

            $table->text('meta_description')->nullable();

            $table->timestamps();
        });

        // Voucher <> Products
        Schema::create('voucher_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id');
            $table->uuid('product_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
        });

        // Voucher <> Categories
        Schema::create('voucher_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
        });

        // Voucher <> Shipping Methods
        Schema::create('voucher_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id');
            $table->uuid('shipping_method_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
        });

        // Voucher <> Payment Methods
        Schema::create('voucher_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('voucher_id');
            $table->uuid('payment_method_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_payment_methods');
        Schema::dropIfExists('voucher_shipping_methods');
        Schema::dropIfExists('voucher_categories');
        Schema::dropIfExists('voucher_products');
        Schema::dropIfExists('vouchers');
    }
};
