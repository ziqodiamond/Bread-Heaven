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
        Schema::create('stores', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Informasi Toko
            |--------------------------------------------------------------------------
            */

            // Nama toko
            $table->string('name');

            // Slug toko
            $table->string('slug')->unique();

            // Deskripsi toko
            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Kontak
            |--------------------------------------------------------------------------
            */

            // Email toko
            $table->string('email')->nullable();

            // Nomor telepon
            $table->string('phone')->nullable();

            // WhatsApp
            $table->string('whatsapp')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Branding
            |--------------------------------------------------------------------------
            */

            // Logo toko
            $table->string('logo')->nullable();

            // Banner toko
            $table->string('banner')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Alamat
            |--------------------------------------------------------------------------
            */

            // Kode provinsi
            $table->string('province_code')->nullable();

            // Nama provinsi
            $table->string('province')->nullable();

            // Kode kota
            $table->string('city_code')->nullable();

            // Nama kota
            $table->string('city')->nullable();

            // Kode kecamatan
            $table->string('district_code')->nullable();

            // Nama kecamatan
            $table->string('district')->nullable();

            // Kode pos
            $table->string('postal_code')->nullable();

            // Detail alamat lengkap
            $table->text('full_address')->nullable();

            // Catatan alamat
            $table->text('address_notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Lokasi Peta / GPS
            |--------------------------------------------------------------------------
            */

            // Latitude Google Maps
            $table->decimal('latitude', 10, 7)->nullable();

            // Longitude Google Maps
            $table->decimal('longitude', 10, 7)->nullable();

            // Link embed Google Maps
            $table->text('google_maps_embed')->nullable();

            // Link Google Maps
            $table->text('google_maps_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Shipping / Pickup
            |--------------------------------------------------------------------------
            */

            // Bisa pickup di toko
            $table->boolean('allow_pickup')
                ->default(false);

            // Default origin Biteship
            $table->boolean('is_shipping_origin')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Sosial Media
            |--------------------------------------------------------------------------
            */

            // Instagram
            $table->string('instagram')->nullable();

            // TikTok
            $table->string('tiktok')->nullable();

            // Facebook
            $table->string('facebook')->nullable();

            // YouTube
            $table->string('youtube')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jam Operasional
            |--------------------------------------------------------------------------
            */

            // Jam buka
            $table->time('open_time')->nullable();

            // Jam tutup
            $table->time('close_time')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            // Meta title
            $table->string('meta_title')->nullable();

            // Meta description
            $table->text('meta_description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            // Status toko aktif
            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
