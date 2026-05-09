<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'order_items';

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
        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Produk
        |--------------------------------------------------------------------------
        */

        'product_name',
        'product_slug',
        'product_sku',
        'product_description',
        'product_image_url',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Harga
        |--------------------------------------------------------------------------
        */

        'product_price',

        /*
        |--------------------------------------------------------------------------
        | Quantity
        |--------------------------------------------------------------------------
        */

        'quantity',

        /*
        |--------------------------------------------------------------------------
        | Berat
        |--------------------------------------------------------------------------
        */

        'product_weight',
        'total_weight',

        /*
        |--------------------------------------------------------------------------
        | Total Harga
        |--------------------------------------------------------------------------
        */

        'subtotal',

        /*
        |--------------------------------------------------------------------------
        | Status Item
        |--------------------------------------------------------------------------
        */

        'status',
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
            'product_price' => 'integer',
            'subtotal' => 'integer',

            // Berat
            'product_weight' => 'integer',
            'total_weight' => 'integer',

            // Quantity
            'quantity' => 'integer',
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

    /**
     * Relasi ke produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek item sudah direfund
     */
    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Mengecek item dibatalkan
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Cancel item order
     */
    public function cancel(): void
    {
        $this->update([

            'status' => 'cancelled',
        ]);
    }

    /**
     * Refund item order
     */
    public function refund(): void
    {
        $this->update([

            'status' => 'refunded',
        ]);
    }
}
