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
        | Messages Table
        |--------------------------------------------------------------------------
        | Menyimpan isi chat conversation
        |--------------------------------------------------------------------------
        */
        Schema::create('messages', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // Relasi conversation
            $table->foreignUuid('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            // Pengirim message
            $table->foreignUuid('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Reply Message
            |--------------------------------------------------------------------------
            */

            // Reply ke message lain
            // Self reference
            $table->uuid('reply_to_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Isi Message
            |--------------------------------------------------------------------------
            */

            // Isi pesan
            $table->longText('message')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tipe Message
            |--------------------------------------------------------------------------
            */

            // text
            // image
            // file
            // system
            // order
            // product
            $table->enum('type', [
                'text',
                'image',
                'file',
                'system',
                'order',
                'product',
            ])->default('text');

            /*
            |--------------------------------------------------------------------------
            | Metadata Message
            |--------------------------------------------------------------------------
            */

            // Message dibaca
            $table->boolean('is_read')
                ->default(false);

            // Message diedit
            $table->boolean('is_edited')
                ->default(false);

            // Message dihapus sender
            $table->boolean('is_deleted')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Timestamp Metadata
            |--------------------------------------------------------------------------
            */

            // Waktu dibaca
            $table->timestamp('read_at')
                ->nullable();

            // Waktu diedit
            $table->timestamp('edited_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Realtime Metadata
            |--------------------------------------------------------------------------
            */

            // UUID realtime client
            $table->string('client_id')
                ->nullable();

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
                'conversation_id',
            ]);

            $table->index([
                'sender_id',
            ]);

            $table->index([
                'reply_to_id',
            ]);

            $table->index([
                'is_read',
            ]);

            $table->index([
                'created_at',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Self Reference Foreign Key
        |--------------------------------------------------------------------------
        | PostgreSQL lebih aman dibuat setelah table selesai dibuat
        |--------------------------------------------------------------------------
        */

        Schema::table('messages', function (Blueprint $table) {

            $table->foreign('reply_to_id')
                ->references('id')
                ->on('messages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
