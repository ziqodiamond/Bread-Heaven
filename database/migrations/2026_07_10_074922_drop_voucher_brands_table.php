<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop voucher_brands table
        Schema::dropIfExists('voucher_brands');
    }

    public function down(): void
    {
        // Recreate voucher_brands table jika di-rollback
        Schema::create('voucher_brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('voucher_id');
            $table->unsignedBigInteger('brand_id');
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
        });
    }
};
