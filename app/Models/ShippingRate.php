<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingRate extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'shipping_rates';

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
        | Relasi
        |--------------------------------------------------------------------------
        */

        'order_id',

        /*
        |--------------------------------------------------------------------------
        | Provider Shipping API
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
        | Informasi Pengiriman
        |--------------------------------------------------------------------------
        */

        'weight',
        'etd',

        /*
        |--------------------------------------------------------------------------
        | Harga Ongkir
        |--------------------------------------------------------------------------
        */

        'price',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Request
        |--------------------------------------------------------------------------
        */

        'origin',
        'destination',

        /*
        |--------------------------------------------------------------------------
        | Response API
        |--------------------------------------------------------------------------
        */

        'response',
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
            'price' => 'integer',

            // Berat
            'weight' => 'integer',

            // JSON response
            'response' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Nama lengkap shipping rate
     * Contoh:
     * JNE REG
     */
    public function getFullNameAttribute(): string
    {
        return strtoupper(
            $this->courier_name . ' ' . $this->service_name
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah rate masih valid
     * Default valid selama 24 jam
     */
    public function isStillValid(
        int $hours = 24
    ): bool {
        return $this->created_at
            ->addHours($hours)
            ->isFuture();
    }
}
