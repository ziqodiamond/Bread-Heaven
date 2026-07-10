<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Add combination rules
            $table->boolean('is_combinable')->default(false)->after('is_stackable')->comment('Can combine with other vouchers');
            $table->enum('combination_type', ['shipping', 'discount', 'both'])->default('both')->after('is_combinable')->comment('Type for combination: shipping, discount, or both');
        });

        // Create pivot table untuk combinations
        Schema::create('voucher_combinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_a_id');
            $table->uuid('voucher_b_id');
            $table->boolean('is_allowed')->default(true);
            $table->string('rule_description')->nullable();
            $table->timestamps();

            $table->foreign('voucher_a_id')->references('id')->on('vouchers')->cascadeOnDelete();
            $table->foreign('voucher_b_id')->references('id')->on('vouchers')->cascadeOnDelete();
            $table->unique(['voucher_a_id', 'voucher_b_id']);
        });

        // Update carts table untuk multiple vouchers
        Schema::table('carts', function (Blueprint $table) {
            // Change single voucher to JSON array
            if (!Schema::hasColumn('carts', 'vouchers')) {
                $table->json('vouchers')->nullable()->after('voucher_snapshot')->comment('Array of applied vouchers');
                $table->integer('total_discount_amount')->default(0)->after('discount_amount')->comment('Total discount from all vouchers');
                $table->integer('total_shipping_discount')->default(0)->after('total_discount_amount')->comment('Total shipping discount');
            }
        });

        // Update orders table untuk multiple vouchers
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'vouchers')) {
                $table->json('vouchers')->nullable()->after('voucher_snapshot')->comment('Array of applied vouchers');
                $table->integer('total_discount_amount')->default(0)->after('discount_amount')->comment('Total discount from all vouchers');
                $table->integer('total_shipping_discount')->default(0)->after('total_discount_amount')->comment('Total shipping discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumnIfExists('vouchers');
            $table->dropColumnIfExists('total_discount_amount');
            $table->dropColumnIfExists('total_shipping_discount');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumnIfExists('vouchers');
            $table->dropColumnIfExists('total_discount_amount');
            $table->dropColumnIfExists('total_shipping_discount');
        });

        Schema::dropIfExists('voucher_combinations');

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumnIfExists('is_combinable');
            $table->dropColumnIfExists('combination_type');
        });
    }
};
