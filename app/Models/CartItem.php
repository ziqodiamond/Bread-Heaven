<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CartItem extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'cart_items';

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

        'cart_id',
        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Quantity
        |--------------------------------------------------------------------------
        */

        'quantity',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

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
     * Relasi ke cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
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
     * Harga asli produk realtime
     */
    public function getOriginalPriceAttribute(): int
    {
        return $this->product?->price ?? 0;
    }

    /**
     * Harga final produk realtime
     */
    public function getProductPriceAttribute(): int
    {
        return $this->product?->final_price ?? 0;
    }

    /**
     * Jumlah discount produk
     */
    public function getDiscountAmountAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->product->discount_amount
            * $this->quantity;
    }

    /**
     * Persentase discount produk
     */
    public function getDiscountPercentageAttribute(): int
    {
        return $this->product?->discount_percentage ?? 0;
    }

    /**
     * Subtotal sebelum discount
     */
    public function getOriginalSubtotalAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->product->price
            * $this->quantity;
    }

    /**
     * Subtotal item cart setelah discount
     */
    public function getSubtotalAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->product->final_price
            * $this->quantity;
    }

    /**
     * Total berat item cart
     */
    public function getTotalWeightAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->product->weight
            * $this->quantity;
    }

    /**
     * Mengecek item sedang discount
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->product?->has_active_discount
            ?? false;
    }

    /**
     * Mengecek item flash sale
     */
    public function getIsFlashSaleAttribute(): bool
    {
        return $this->product?->is_flash_sale
            ?? false;
    }

    /**
     * Nama produk realtime
     */
    public function getProductNameAttribute(): ?string
    {
        return $this->product?->name;
    }

    /**
     * SKU produk realtime
     */
    public function getProductSkuAttribute(): ?string
    {
        return $this->product?->sku;
    }

    /**
     * Thumbnail produk realtime
     */
    public function getProductImageUrlAttribute(): ?string
    {
        return $this->product?->thumbnail;
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
        | Refresh cart summary setelah create/update/delete
        |--------------------------------------------------------------------------
        */
        static::saved(function ($cartItem) {

            $cartItem->cart?->refreshCartSummary();
        });

        static::deleted(function ($cartItem) {

            $cartItem->cart?->refreshCartSummary();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Update quantity item cart
     */
    public function updateQuantity(
        int $quantity
    ): void {
        $this->update([
            'quantity' => $quantity,
        ]);
    }

    /**
     * Mengecek stok produk cukup
     */
    public function hasEnoughStock(): bool
    {
        if (!$this->product) {
            return false;
        }

        return $this->product
            ->hasEnoughStock($this->quantity);
    }
}
