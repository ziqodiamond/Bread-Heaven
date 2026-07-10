<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Voucher <> Products (include/exclude)
        Schema::create('voucher_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->uuid('product_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

        });

        // Voucher <> Categories
        Schema::create('voucher_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

        });

        // Voucher <> Brands
        Schema::create('voucher_brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('brand_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

        });

        // Voucher <> Shipping Methods
        Schema::create('voucher_shipping_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('shipping_method_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

        });

        // Voucher <> Payment Methods
        Schema::create('voucher_payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_payment_methods');
        Schema::dropIfExists('voucher_shipping_methods');
        Schema::dropIfExists('voucher_brands');
        Schema::dropIfExists('voucher_categories');
        Schema::dropIfExists('voucher_products');
    }
};