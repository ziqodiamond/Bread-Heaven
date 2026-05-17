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
        | Conversations Table
        |--------------------------------------------------------------------------
        | Menyimpan ruang chat customer & admin
        |--------------------------------------------------------------------------
        */
        Schema::create('conversations', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Conversation
            |--------------------------------------------------------------------------
            */

            // customer_support
            // product_question
            // complaint
            // order_support
            $table->enum('type', [
                'customer_support',
                'product_question',
                'complaint',
                'order_support',
            ])->default('customer_support');

            // Judul chat
            $table->string('subject')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Relasi User
            |--------------------------------------------------------------------------
            */

            // Customer pembuat chat
            $table->foreignUuid('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Admin/CS yang menangani
            $table->foreignUuid('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Relasi Optional
            |--------------------------------------------------------------------------
            */

            // Relasi order
            // Untuk komplain order
            $table->foreignUuid('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // Relasi produk
            // Untuk pertanyaan produk
            $table->foreignUuid('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Last Message
            |--------------------------------------------------------------------------
            */

            // Isi last message
            $table->text('last_message')
                ->nullable();

            // Waktu last message
            $table->timestamp('last_message_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Unread Counter
            |--------------------------------------------------------------------------
            */

            // Total unread customer
            $table->unsignedInteger('customer_unread_count')
                ->default(0);

            // Total unread admin
            $table->unsignedInteger('admin_unread_count')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status Conversation
            |--------------------------------------------------------------------------
            */

            // open
            // resolved
            // closed
            $table->enum('status', [
                'open',
                'resolved',
                'closed',
            ])->default('open');

            /*
            |--------------------------------------------------------------------------
            | Priority Conversation
            |--------------------------------------------------------------------------
            */

            // low
            // normal
            // high
            // urgent
            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'urgent',
            ])->default('normal');

            /*
            |--------------------------------------------------------------------------
            | Realtime Metadata
            |--------------------------------------------------------------------------
            */

            // Customer sedang mengetik
            $table->boolean('customer_typing')
                ->default(false);

            // Admin sedang mengetik
            $table->boolean('admin_typing')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Flags
            |--------------------------------------------------------------------------
            */

            // Chat dibaca admin
            $table->boolean('is_read_by_admin')
                ->default(false);

            // Chat dibaca customer
            $table->boolean('is_read_by_customer')
                ->default(false);

            // Chat dipin admin
            $table->boolean('is_pinned')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            */

            $table->softDeletes();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'customer_id',
            ]);

            $table->index([
                'admin_id',
            ]);

            $table->index([
                'order_id',
            ]);

            $table->index([
                'product_id',
            ]);

            $table->index([
                'status',
            ]);

            $table->index([
                'priority',
            ]);

            $table->index([
                'last_message_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
