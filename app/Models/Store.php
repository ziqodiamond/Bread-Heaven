<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'stores';

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
        | Informasi Toko
        |--------------------------------------------------------------------------
        */

        'name',
        'slug',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Kontak
        |--------------------------------------------------------------------------
        */

        'email',
        'phone',
        'whatsapp',

        /*
        |--------------------------------------------------------------------------
        | Branding
        |--------------------------------------------------------------------------
        */

        'logo',
        'banner',

        /*
        |--------------------------------------------------------------------------
        | Alamat
        |--------------------------------------------------------------------------
        */

        'province_code',
        'province',

        'city_code',
        'city',

        'district_code',
        'district',

        'postal_code',

        'full_address',
        'address_notes',

        /*
        |--------------------------------------------------------------------------
        | Lokasi GPS / Peta
        |--------------------------------------------------------------------------
        */

        'latitude',
        'longitude',

        'google_maps_embed',
        'google_maps_url',

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */

        'allow_pickup',
        'is_shipping_origin',

        /*
        |--------------------------------------------------------------------------
        | Sosial Media
        |--------------------------------------------------------------------------
        */

        'instagram',
        'tiktok',
        'facebook',
        'youtube',

        /*
        |--------------------------------------------------------------------------
        | Jam Operasional
        |--------------------------------------------------------------------------
        */

        'open_time',
        'close_time',

        /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

        'meta_title',
        'meta_description',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // Boolean
            'allow_pickup' => 'boolean',
            'is_shipping_origin' => 'boolean',
            'is_active' => 'boolean',

            // GPS coordinate
            'latitude' => 'float',
            'longitude' => 'float',

            // Jam operasional
            'open_time' => 'datetime:H:i',
            'close_time' => 'datetime:H:i',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Full alamat toko
     */
    public function getFullAddressTextAttribute(): string
    {
        return collect([

            $this->full_address,

            $this->district,

            $this->city,

            $this->province,

            $this->postal_code,

        ])->filter()->implode(', ');
    }

    /**
     * URL logo toko
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : null;
    }

    /**
     * URL banner toko
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner
            ? asset('storage/' . $this->banner)
            : null;
    }

    /**
     * Link Google Maps otomatis
     */
    public function getMapsLinkAttribute(): ?string
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return 'https://www.google.com/maps?q=' .
            $this->latitude .
            ',' .
            $this->longitude;
    }

    /**
     * Mengecek toko aktif
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Toko aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Origin shipping utama
     */
    public function scopeShippingOrigin($query)
    {
        return $query->where(
            'is_shipping_origin',
            true
        );
    }
}
