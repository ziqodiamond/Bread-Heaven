<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'shipping_methods';

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
        | Provider Shipping
        |--------------------------------------------------------------------------
        */

        'provider',

        /*
        |--------------------------------------------------------------------------
        | Informasi Courier
        |--------------------------------------------------------------------------
        */

        'courier_name',
        'courier_code',

        /*
        |--------------------------------------------------------------------------
        | Informasi Service
        |--------------------------------------------------------------------------
        */

        'service_name',
        'service_code',

        /*
        |--------------------------------------------------------------------------
        | Deskripsi
        |--------------------------------------------------------------------------
        */

        'description',

        /*
        |--------------------------------------------------------------------------
        | Estimasi Pengiriman
        |--------------------------------------------------------------------------
        */

        'estimated_delivery',

        /*
        |--------------------------------------------------------------------------
        | Fee Tambahan
        |--------------------------------------------------------------------------
        */

        'additional_fee',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Metadata Tambahan
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
            'additional_fee' => 'integer',

            // JSON metadata
            'metadata' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Nama lengkap shipping method
     * Contoh:
     * JNE REG
     */
    public function getFullNameAttribute(): string
    {
        return strtoupper(
            $this->courier_name . ' ' . $this->service_name
        );
    }

    /**
     * Mengecek shipping method aktif
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Shipping method aktif
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
     * Filter courier
     */
    public function scopeCourier($query, string $courier)
    {
        return $query->where('courier_code', $courier);
    }
}
