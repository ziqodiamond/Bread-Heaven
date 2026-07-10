<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Cart extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'carts';

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
        | Status Cart
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Informasi Cart
        |--------------------------------------------------------------------------
        */

        'total_items',
        'total_quantity',

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Harga
        |--------------------------------------------------------------------------
        */

        // Subtotal sebelum discount
        'subtotal',

        // Total discount
        'discount_amount',

        // Total setelah discount
        'final_subtotal',

        /*
        |--------------------------------------------------------------------------
        | Voucher
        |--------------------------------------------------------------------------
        */

        'voucher_code',
        'voucher_name',
        'voucher_snapshot',

        /*
        |--------------------------------------------------------------------------
        | Expired Cart
        |--------------------------------------------------------------------------
        */

        'expired_at',
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
            | Harga
            |--------------------------------------------------------------------------
            */

            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'final_subtotal' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'expired_at' => 'datetime',
            'voucher_snapshot' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke user pemilik cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi item dalam cart
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah cart kosong
     */
    public function getIsEmptyAttribute(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Total quantity seluruh item
     */
    public function getTotalItemQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Mengecek cart memakai voucher
     */
    public function getHasVoucherAttribute(): bool
    {
        return !empty($this->voucher_code);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Refresh summary cart
     */
    public function refreshCartSummary(): void
    {
        $items = $this->items;

        /*
        |--------------------------------------------------------------------------
        | Hitung subtotal item
        |--------------------------------------------------------------------------
        */

        $subtotal = $items->sum('subtotal');

        /*
        |--------------------------------------------------------------------------
        | Discount cart
        |--------------------------------------------------------------------------
        | Nanti akan dihandle oleh DiscountService
        |--------------------------------------------------------------------------
        */

        $discountAmount = $this->discount_amount ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Final subtotal
        |--------------------------------------------------------------------------
        */

        $finalSubtotal = max(
            0,
            $subtotal - $discountAmount
        );

        /*
        |--------------------------------------------------------------------------
        | Update cart summary
        |--------------------------------------------------------------------------
        */

        $this->update([

            'total_items' => $items->count(),

            'total_quantity' => $items->sum('quantity'),

            'subtotal' => $subtotal,

            'final_subtotal' => $finalSubtotal,
        ]);
    }

    /**
     * Hapus voucher cart
     */
    public function clearVoucher(): void
    {
        $this->update([

            'voucher_code' => null,
            'voucher_name' => null,

            'discount_amount' => 0,
        ]);

        $this->refreshCartSummary();
    }
}
