<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'user_addresses';

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
        | Relasi User
        |--------------------------------------------------------------------------
        */

        'user_id',

        /*
        |--------------------------------------------------------------------------
        | Informasi Penerima
        |--------------------------------------------------------------------------
        */

        'receiver_name',
        'receiver_phone',

        /*
        |--------------------------------------------------------------------------
        | Informasi Wilayah
        |--------------------------------------------------------------------------
        */

        'province_code',
        'province',

        'city_code',
        'city',

        'district_code',
        'district',

        'postal_code',

        /*
        |--------------------------------------------------------------------------
        | Detail Alamat
        |--------------------------------------------------------------------------
        */

        'full_address',
        'notes',

        /*
        |--------------------------------------------------------------------------
        | Lokasi GPS
        |--------------------------------------------------------------------------
        */

        'latitude',
        'longitude',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_default',
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
            'is_default' => 'boolean',
            'is_active' => 'boolean',

            // GPS coordinate
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
     * Alamat lengkap format text
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
     * Label alamat
     */
    public function getLabelAttribute(): string
    {
        return $this->receiver_name .
            ' - ' .
            $this->city;
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Methods
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan hanya ada 1 alamat default per user
        |--------------------------------------------------------------------------
        */
        static::saving(function ($address) {

            if ($address->is_default) {

                static::where('user_id', $address->user_id)

                    ->where('id', '!=', $address->id)

                    ->update([
                        'is_default' => false,
                    ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Alamat aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Alamat default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Set sebagai alamat default
     */
    public function setAsDefault(): void
    {
        static::where('user_id', $this->user_id)

            ->update([
                'is_default' => false,
            ]);

        $this->update([
            'is_default' => true,
        ]);
    }
}
