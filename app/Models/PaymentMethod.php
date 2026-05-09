<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'payment_methods';

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

        /*
        |--------------------------------------------------------------------------
        | Informasi Payment Method
        |--------------------------------------------------------------------------
        */

        'name',
        'code',
        'category',

        /*
        |--------------------------------------------------------------------------
        | Provider
        |--------------------------------------------------------------------------
        */

        'provider',

        /*
        |--------------------------------------------------------------------------
        | Manual Transfer
        |--------------------------------------------------------------------------
        */

        'account_number',
        'account_name',

        /*
        |--------------------------------------------------------------------------
        | Fee
        |--------------------------------------------------------------------------
        */

        'fee',

        /*
        |--------------------------------------------------------------------------
        | Media
        |--------------------------------------------------------------------------
        */

        'image_url',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------------------
        */

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // Harga bigint
            'fee' => 'integer',

            // Metadata JSON
            'metadata' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke orders
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
     * Mengecek payment method aktif
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Mengecek payment manual transfer
     */
    public function getIsManualAttribute(): bool
    {
        return $this->provider === 'manual';
    }

    /**
     * Mengecek payment Midtrans
     */
    public function getIsMidtransAttribute(): bool
    {
        return $this->provider === 'midtrans';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Payment method aktif
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Filter provider
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Filter category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
