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

        // Harga asli produk
        'original_price',

        // Harga final setelah discount
        'product_price',

        // Total discount
        'discount_amount',

        // Persentase discount
        'discount_percentage',

        /*
        |--------------------------------------------------------------------------
        | Informasi Discount
        |--------------------------------------------------------------------------
        */

        'discount_label',
        'discount_source',

        // Voucher info
        'voucher_ids',
        'voucher_discount_amount',

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

        // Subtotal sebelum discount
        'original_subtotal',

        // Subtotal final
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

            /*
            |--------------------------------------------------------------------------
            | Harga bigint
            |--------------------------------------------------------------------------
            */

            'original_price' => 'integer',

            'product_price' => 'integer',

            'discount_amount' => 'integer',

            'original_subtotal' => 'integer',

            'subtotal' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Berat
            |--------------------------------------------------------------------------
            */

            'product_weight' => 'integer',

            'total_weight' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'quantity' => 'integer',

            'discount_percentage' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Voucher Info
            |--------------------------------------------------------------------------
            */

            'voucher_ids' => 'json',

            'voucher_discount_amount' => 'integer',
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
        return $this->belongsTo(
            Order::class
        );
    }

    /**
     * Relasi ke produk
     */
    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
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

    /**
     * Mengecek item memiliki discount
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Mengecek item flash sale
     */
    public function getIsFlashSaleAttribute(): bool
    {
        return $this->discount_source === 'flash_sale';
    }

    /**
     * Total discount item
     */
    public function getTotalDiscountAttribute(): int
    {
        return
            $this->original_subtotal -
            $this->subtotal;
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

    /**
     * Restore item order
     */
    public function restoreItem(): void
    {
        $this->update([

            'status' => 'active',
        ]);
    }
}
