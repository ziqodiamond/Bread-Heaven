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
        | Payment Transactions Table
        |--------------------------------------------------------------------------
        | Menyimpan histori transaksi payment gateway
        | Contoh:
        | - Midtrans
        | - Xendit
        |--------------------------------------------------------------------------
        */
        Schema::create('payment_transactions', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi Order
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Gateway
            |--------------------------------------------------------------------------
            */

            // Provider payment gateway
            // Contoh:
            // midtrans
            $table->string('gateway');

            // Transaction ID dari gateway
            $table->string('gateway_transaction_id')
                ->nullable()
                ->index();

            // Order ID/reference dari gateway
            $table->string('gateway_order_id')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Informasi Payment
            |--------------------------------------------------------------------------
            */

            // Jenis pembayaran
            // Contoh:
            // qris
            // bank_transfer
            // gopay
            $table->string('payment_type')->nullable();

            // Nama bank
            // Contoh:
            // bca
            // bri
            $table->string('bank')->nullable();

            // Virtual account number
            $table->string('va_number')->nullable();

            // Bill key
            $table->string('bill_key')->nullable();

            // Biller code
            $table->string('biller_code')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informasi Harga
            |--------------------------------------------------------------------------
            */

            // Total pembayaran
            // Menggunakan bigint
            $table->bigInteger('gross_amount');

            // Mata uang
            $table->string('currency')->default('IDR');

            /*
            |--------------------------------------------------------------------------
            | Status Transaksi
            |--------------------------------------------------------------------------
            */

            // Status payment gateway
            // Contoh:
            // pending
            // settlement
            // expire
            // cancel
            $table->string('transaction_status')->nullable();

            // Fraud status dari gateway
            $table->string('fraud_status')->nullable();

            /*
            |--------------------------------------------------------------------------
            | URL Pembayaran
            |--------------------------------------------------------------------------
            */

            // Snap token Midtrans
            $table->string('snap_token')->nullable();

            // Redirect URL payment
            $table->text('payment_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Expired Payment
            |--------------------------------------------------------------------------
            */

            // Expired pembayaran
            $table->timestamp('expired_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Payload Gateway
            |--------------------------------------------------------------------------
            | Menyimpan seluruh response gateway
            |--------------------------------------------------------------------------
            */

            $table->json('payload')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp Pembayaran
            |--------------------------------------------------------------------------
            */

            // Waktu pembayaran berhasil
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
