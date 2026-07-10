<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->boolean('allow_on_flash_sale')->default(true)->after('members_only');
            $table->boolean('allow_on_discount')->default(true)->after('allow_on_flash_sale');
            $table->boolean('exclude_digital')->default(false)->after('allow_on_discount');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('allow_on_flash_sale');
            $table->dropColumn('allow_on_discount');
            $table->dropColumn('exclude_digital');
        });
    }
};