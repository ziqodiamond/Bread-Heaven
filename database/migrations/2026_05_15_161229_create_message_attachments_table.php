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
        | Message Attachments Table
        |--------------------------------------------------------------------------
        | Menyimpan file attachment chat
        |--------------------------------------------------------------------------
        */
        Schema::create('message_attachments', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // Relasi message
            $table->foreignUuid('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi File
            |--------------------------------------------------------------------------
            */

            // Nama asli file
            $table->string('file_name');

            // Nama file storage
            $table->string('stored_file_name');

            // Path file
            $table->string('file_path');

            /*
            |--------------------------------------------------------------------------
            | Metadata File
            |--------------------------------------------------------------------------
            */

            // MIME type
            // image/png
            // application/pdf
            $table->string('mime_type');

            // Extension file
            // png
            // jpg
            // pdf
            $table->string('extension')
                ->nullable();

            // Size file dalam bytes
            $table->unsignedBigInteger('file_size')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Tipe Attachment
            |--------------------------------------------------------------------------
            */

            // image
            // video
            // file
            // audio
            $table->enum('type', [
                'image',
                'video',
                'file',
                'audio',
            ])->default('file');

            /*
            |--------------------------------------------------------------------------
            | Metadata Media
            |--------------------------------------------------------------------------
            */

            // Width image/video
            $table->unsignedInteger('width')
                ->nullable();

            // Height image/video
            $table->unsignedInteger('height')
                ->nullable();

            // Duration audio/video
            $table->unsignedInteger('duration')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Flags
            |--------------------------------------------------------------------------
            */

            // Attachment deleted
            $table->boolean('is_deleted')
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
                'message_id',
            ]);

            $table->index([
                'type',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
