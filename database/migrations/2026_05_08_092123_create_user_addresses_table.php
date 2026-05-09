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
        | User Addresses Table
        |--------------------------------------------------------------------------
        | Menyimpan daftar alamat user
        | Digunakan untuk:
        | - checkout
        | - pengiriman
        | - cek ongkir
        |--------------------------------------------------------------------------
        */
        Schema::create('user_addresses', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Relasi User
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Penerima
            |--------------------------------------------------------------------------
            */

            // Nama penerima
            $table->string('receiver_name');

            // Nomor telepon penerima
            $table->string('receiver_phone');

            /*
            |--------------------------------------------------------------------------
            | Informasi Wilayah
            |--------------------------------------------------------------------------
            | Dibutuhkan untuk shipping API
            |--------------------------------------------------------------------------
            */

            // Province ID dari API shipping
            $table->string('province_code')->nullable();

            // Nama provinsi
            $table->string('province');

            // City ID dari API shipping
            $table->string('city_code')->nullable();

            // Nama kota
            $table->string('city');

            // District ID dari API shipping
            $table->string('district_code')->nullable();

            // Nama kecamatan
            $table->string('district');

            // Postal code
            $table->string('postal_code');

            /*
            |--------------------------------------------------------------------------
            | Detail Alamat
            |--------------------------------------------------------------------------
            */

            // Alamat lengkap
            $table->text('full_address');

            // Catatan tambahan
            // Contoh:
            // Rumah pagar hitam
            $table->text('notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Lokasi GPS
            |--------------------------------------------------------------------------
            | Optional untuk shipping modern
            |--------------------------------------------------------------------------
            */

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            // Alamat utama user
            $table->boolean('is_default')->default(false);

            // Status aktif alamat
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
