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
        | Webhooks Table
        |--------------------------------------------------------------------------
        | Menyimpan seluruh webhook event dari external service
        | Contoh:
        | - Midtrans
        | - Biteship
        |--------------------------------------------------------------------------
        */
        Schema::create('webhooks', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Provider Webhook
            |--------------------------------------------------------------------------
            */

            // Provider webhook
            // Contoh:
            // midtrans
            // biteship
            $table->string('provider');

            /*
            |--------------------------------------------------------------------------
            | Event Webhook
            |--------------------------------------------------------------------------
            */

            // Jenis event webhook
            // Contoh:
            // payment.settlement
            // payment.expired
            // shipment.delivered
            $table->string('event_type');

            /*
            |--------------------------------------------------------------------------
            | Reference Data
            |--------------------------------------------------------------------------
            */

            // Reference ID external
            // Contoh:
            // transaction id
            // shipment id
            $table->string('reference_id')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Status Processing
            |--------------------------------------------------------------------------
            */

            // Status webhook sudah diproses atau belum
            $table->boolean('processed')
                ->default(false);

            // Waktu webhook diproses
            $table->timestamp('processed_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informasi Request
            |--------------------------------------------------------------------------
            */

            // HTTP method
            $table->string('method')
                ->nullable();

            // Signature key/header
            $table->text('signature')
                ->nullable();

            // Source IP webhook
            $table->string('ip_address', 45)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Payload Webhook
            |--------------------------------------------------------------------------
            | Menyimpan raw payload dari provider
            |--------------------------------------------------------------------------
            */

            $table->json('payload');

            /*
            |--------------------------------------------------------------------------
            | Error Handling
            |--------------------------------------------------------------------------
            */

            // Pesan error jika gagal diproses
            $table->text('error_message')
                ->nullable();

            // Jumlah retry processing
            $table->unsignedInteger('retry_count')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
