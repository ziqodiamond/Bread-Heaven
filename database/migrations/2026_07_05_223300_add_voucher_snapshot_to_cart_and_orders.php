<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->json('voucher_snapshot')->nullable()->after('voucher_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->json('voucher_snapshot')->nullable()->after('voucher_value');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('voucher_snapshot');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('voucher_snapshot');
        });
    }
};