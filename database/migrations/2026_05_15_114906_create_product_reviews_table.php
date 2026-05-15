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
        Schema::create('product_reviews', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Rating & Review
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('rating');

            $table->text('review')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_anonymous')
                ->default(false);

            $table->boolean('is_visible')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Balasan Admin
            |--------------------------------------------------------------------------
            */

            $table->text('admin_reply')
                ->nullable();

            $table->timestamp('admin_replied_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Moderation
            |--------------------------------------------------------------------------
            */

            $table->timestamp('hidden_at')
                ->nullable();

            $table->text('hidden_reason')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('rating');

            $table->index('product_id');

            $table->index('user_id');

            $table->index('is_visible');

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Review
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'user_id',
                'order_item_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
