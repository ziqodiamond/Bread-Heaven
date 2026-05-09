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
        | Orders Table
        |--------------------------------------------------------------------------
        | Menyimpan data transaksi/order customer
        |--------------------------------------------------------------------------
        */
        Schema::create('orders', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            // User pemilik order
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Alamat pengiriman
            $table->foreignUuid('user_address_id')
                ->nullable()
                ->constrained('user_addresses')
                ->nullOnDelete();

            // Payment method
            $table->foreignUuid('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Invoice
            |--------------------------------------------------------------------------
            */

            // Nomor invoice unik
            // Contoh:
            // INV-20260508-XXXX
            $table->string('invoice_number')->unique();

            /*
            |--------------------------------------------------------------------------
            | Informasi Customer
            |--------------------------------------------------------------------------
            | Snapshot data customer saat checkout
            |--------------------------------------------------------------------------
            */

            $table->string('customer_name');

            $table->string('customer_email');

            $table->string('customer_phone');

            /*
            |--------------------------------------------------------------------------
            | Snapshot Alamat Pengiriman
            |--------------------------------------------------------------------------
            */

            $table->string('shipping_receiver_name');

            $table->string('shipping_receiver_phone');

            $table->string('shipping_province');

            $table->string('shipping_city');

            $table->string('shipping_district');

            $table->string('shipping_postal_code');

            $table->text('shipping_full_address');

            $table->text('shipping_notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informasi Shipping
            |--------------------------------------------------------------------------
            */

            // Nama courier
            // Contoh:
            // jne
            // jnt
            // sicepat
            $table->string('shipping_courier');

            // Service courier
            // Contoh:
            // REG
            // YES
            $table->string('shipping_service');

            // Estimasi pengiriman
            // Contoh:
            // 2-3 days
            $table->string('shipping_etd')->nullable();

            // Nomor resi manual
            $table->string('tracking_number')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Perhitungan Harga
            |--------------------------------------------------------------------------
            | Menggunakan bigint agar aman
            |--------------------------------------------------------------------------
            */

            // Total harga produk
            $table->bigInteger('subtotal')->default(0);

            // Total ongkir
            $table->bigInteger('shipping_cost')->default(0);

            // Biaya admin payment
            $table->bigInteger('service_fee')->default(0);

            // Total diskon
            $table->bigInteger('discount_amount')->default(0);

            // Total akhir pembayaran
            $table->bigInteger('grand_total')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Informasi Berat
            |--------------------------------------------------------------------------
            */

            // Total berat order dalam gram
            $table->unsignedInteger('total_weight')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Payment Gateway
            |--------------------------------------------------------------------------
            */

            // Provider payment
            // Contoh:
            // midtrans
            $table->string('payment_gateway')->nullable();

            // Reference dari payment gateway
            $table->string('payment_reference')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Order
            |--------------------------------------------------------------------------
            */

            $table->enum('order_status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Status Pembayaran
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'paid',
                'failed',
                'expired',
                'refunded'
            ])->default('unpaid');

            /*
            |--------------------------------------------------------------------------
            | Catatan Order
            |--------------------------------------------------------------------------
            */

            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp Order
            |--------------------------------------------------------------------------
            */

            $table->timestamp('paid_at')->nullable();

            $table->timestamp('shipped_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
