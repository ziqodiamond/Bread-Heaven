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
        | Vouchers Table
        |--------------------------------------------------------------------------
        | Menyimpan voucher / coupon ecommerce
        |--------------------------------------------------------------------------
        */
        Schema::create('vouchers', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Voucher
            |--------------------------------------------------------------------------
            */

            // Nama voucher
            // Contoh:
            // Promo Ramadhan
            // Gratis Ongkir
            $table->string('name');

            // Kode voucher
            // Contoh:
            // RAMADHAN10
            // FREESHIP
            $table->string('code')
                ->unique();

            // Deskripsi voucher
            $table->longText('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tipe Voucher
            |--------------------------------------------------------------------------
            */

            // fixed         = potongan langsung
            // percent       = diskon persen
            // free_shipping = gratis ongkir
            $table->enum('type', [
                'fixed',
                'percent',
                'free_shipping',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Nilai Voucher
            |--------------------------------------------------------------------------
            */

            // Nilai voucher
            // Contoh:
            // 10 = 10%
            // 5000 = Rp5.000
            $table->bigInteger('value')
                ->default(0);

            // Maksimal potongan
            // Berguna untuk voucher persen
            $table->bigInteger('maximum_discount')
                ->nullable();

            // Minimal belanja
            $table->bigInteger('minimum_purchase')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Limit Voucher
            |--------------------------------------------------------------------------
            */

            // Total quota voucher
            $table->unsignedInteger('quota')
                ->nullable();

            // Total voucher terpakai
            $table->unsignedInteger('used_count')
                ->default(0);

            // Limit penggunaan per user
            $table->unsignedInteger('max_usage_per_user')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Status Voucher
            |--------------------------------------------------------------------------
            */

            // Draft / publish
            $table->enum('status', [
                'draft',
                'active',
                'expired',
                'disabled',
            ])->default('draft');

            // Aktif/nonaktif
            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Jadwal Voucher
            |--------------------------------------------------------------------------
            */

            // Waktu mulai voucher
            $table->timestamp('start_at')
                ->nullable();

            // Waktu selesai voucher
            $table->timestamp('end_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengaturan Voucher
            |--------------------------------------------------------------------------
            */

            // Bisa digabung discount lain
            $table->boolean('is_stackable')
                ->default(false);

            // Hanya untuk user login
            $table->boolean('members_only')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Tampilan Voucher
            |--------------------------------------------------------------------------
            */

            // Label promo
            $table->string('label')
                ->nullable();

            // Warna badge
            $table->string('badge_color')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            // Total views voucher
            $table->unsignedBigInteger('total_views')
                ->default(0);

            // Total claim voucher
            $table->unsignedBigInteger('total_claims')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            $table->string('meta_title')
                ->nullable();

            $table->text('meta_description')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
