<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $keyType = 'string';

    public $incrementing = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        // Informasi user
        'name',
        'email',
        'phone',

        // Authentication
        'password',

        // Role
        'role',

        // Foto profil
        'profile_photo_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [

        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // Email verification
            'email_verified_at' => 'datetime',

            // Auto hashing password
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi cart user
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relasi item cart user
     */
    public function cartItems()
    {
        return $this->hasManyThrough(
            CartItem::class,
            Cart::class,
            'user_id',
            'cart_id'
        );
    }

    /**
     * Relasi alamat user
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Relasi order user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek user admin
     */
    public function getIsAdminAttribute(): bool
    {
        return in_array(
            $this->role,
            ['admin', 'super_admin']
        );
    }

    /**
     * Mengecek super admin
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * URL foto profil
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : asset('images/guest.jpg');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan alamat default user
     */
    public function defaultAddress()
    {
        return $this->addresses()
            ->where('is_default', true)
            ->first();
    }

    /**
     * Mendapatkan cart aktif user
     */
    public function activeCart()
    {
        return $this->cart()
            ->where('status', 'active')
            ->first();
    }
}
