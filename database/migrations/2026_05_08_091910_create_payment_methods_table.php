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
        | Payment Methods Table
        |--------------------------------------------------------------------------
        | Menyimpan daftar metode pembayaran
        | Digunakan untuk:
        | - Midtrans
        | - Manual Transfer
        | - COD
        |--------------------------------------------------------------------------
        */
        Schema::create('payment_methods', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Payment Method
            |--------------------------------------------------------------------------
            */

            // Nama payment method
            // Contoh:
            // QRIS
            // BCA Virtual Account
            // GoPay
            $table->string('name');

            // Code unik internal
            // Contoh:
            // qris
            // bca_va
            // gopay
            $table->string('code')->unique();

            // Kategori payment
            // Contoh:
            // ewallet
            // virtual_account
            // bank_transfer
            // cod
            $table->string('category');

            /*
            |--------------------------------------------------------------------------
            | Provider / Gateway
            |--------------------------------------------------------------------------
            */

            // Contoh:
            // midtrans
            // manual
            $table->string('provider');

            /*
            |--------------------------------------------------------------------------
            | Manual Transfer
            |--------------------------------------------------------------------------
            */

            // Nomor rekening
            $table->string('account_number')->nullable();

            // Nama pemilik rekening
            $table->string('account_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fee Payment
            |--------------------------------------------------------------------------
            */

            // Biaya admin payment
            // Menggunakan bigint
            // Contoh:
            // 2500 = Rp2.500
            $table->bigInteger('fee')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            // Logo payment method
            $table->string('image_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'available',
                'unavailable'
            ])->default('available');

            /*
            |--------------------------------------------------------------------------
            | Metadata Tambahan
            |--------------------------------------------------------------------------
            */

            // Untuk menyimpan config tambahan
            // seperti:
            // - kode VA
            // - expiry
            // - config gateway
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
