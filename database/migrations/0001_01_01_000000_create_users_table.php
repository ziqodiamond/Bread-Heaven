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
        | Users Table
        |--------------------------------------------------------------------------
        | Menyimpan data user aplikasi
        |--------------------------------------------------------------------------
        */
        Schema::create('users', function (Blueprint $table) {

            // Primary UUID
            $table->uuid('id')->primary();

            // Informasi user
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // Verifikasi email
            $table->timestamp('email_verified_at')->nullable();

            // Authentication
            $table->string('password');
            $table->rememberToken();

            // Role user
            $table->enum('role', [
                'user',
                'admin',
                'super_admin'
            ])->default('user');

            // Foto profil
            $table->string('profile_photo_path')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Password Reset Tokens
        |--------------------------------------------------------------------------
        */
        Schema::create('password_reset_tokens', function (Blueprint $table) {

            $table->string('email')->primary();

            $table->string('token');

            $table->timestamp('created_at')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | Sessions Table
        |--------------------------------------------------------------------------
        | Menyimpan session login user
        |--------------------------------------------------------------------------
        */
        Schema::create('sessions', function (Blueprint $table) {

            // Session ID bawaan Laravel
            $table->string('id')->primary();

            // Relasi ke user UUID
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Informasi device user
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Payload session
            $table->longText('payload');

            // Activity timestamp
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('users');
    }
};
