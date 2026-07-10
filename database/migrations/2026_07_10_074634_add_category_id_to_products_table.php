<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add category_id foreign key
            $table->unsignedBigInteger('category_id')->nullable()->after('sku');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Drop the old category column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->dropForeignIdFor(Category::class);
            $table->dropColumn('category_id');
        });
    }
};
