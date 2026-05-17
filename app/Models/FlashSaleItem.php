<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashSaleItem extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'flash_sale_items';

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

        'flash_sale_id',
        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Produk
        |--------------------------------------------------------------------------
        */

        'product_name',
        'product_sku',
        'product_image_url',

        /*
        |--------------------------------------------------------------------------
        | Harga Flash Sale
        |--------------------------------------------------------------------------
        */

        'original_price',
        'sale_price',

        'discount_type',
        'discount_value',
        'discount_percentage',

        /*
        |--------------------------------------------------------------------------
        | Stock Flash Sale
        |--------------------------------------------------------------------------
        */

        'stock_limit',
        'sold_quantity',
        'max_purchase_per_user',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Informasi Tambahan
        |--------------------------------------------------------------------------
        */

        'sort_order',
        'badge_label',

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        'total_views',
        'total_checkouts',
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

            'original_price' => 'integer',

            'sale_price' => 'integer',

            'discount_value' => 'integer',

            'discount_percentage' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Stock
            |--------------------------------------------------------------------------
            */

            'stock_limit' => 'integer',

            'sold_quantity' => 'integer',

            'max_purchase_per_user' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            'total_views' => 'integer',

            'total_checkouts' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            'is_active' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Sorting
            |--------------------------------------------------------------------------
            */

            'sort_order' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi flash sale
     */
    public function flashSale()
    {
        return $this->belongsTo(
            FlashSale::class
        );
    }

    /**
     * Relasi produk
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
     * Mengecek item flash sale aktif
     */
    public function getIsRunningAttribute(): bool
    {
        return
            $this->is_active &&
            $this->flashSale?->is_running;
    }

    /**
     * Sisa stok flash sale
     */
    public function getRemainingStockAttribute(): int
    {
        return max(
            0,
            $this->stock_limit -
                $this->sold_quantity
        );
    }

    /**
     * Mengecek stok habis
     */
    public function getIsSoldOutAttribute(): bool
    {
        return $this->remaining_stock <= 0;
    }

    /**
     * Progress penjualan flash sale
     */
    public function getSoldPercentageAttribute(): int
    {
        if ($this->stock_limit <= 0) {
            return 0;
        }

        return (int) round(
            (
                $this->sold_quantity /
                $this->stock_limit
            ) * 100
        );
    }

    /**
     * Total discount produk
     */
    public function getDiscountAmountAttribute(): int
    {
        return
            $this->original_price -
            $this->sale_price;
    }

    /**
     * Thumbnail produk
     */
    public function getThumbnailAttribute(): ?string
    {
        return $this->product_image_url
            ? asset(
                'storage/' .
                    $this->product_image_url
            )
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Item aktif
     */
    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

    /**
     * Item tersedia
     */
    public function scopeAvailable($query)
    {
        return $query

            ->active()

            ->whereRaw(
                'sold_quantity < stock_limit'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Increment stok terjual
     */
    public function incrementSold(
        int $quantity = 1
    ): void {
        $this->increment(
            'sold_quantity',
            $quantity
        );

        $this->increment(
            'total_checkouts',
            $quantity
        );
    }

    /**
     * Decrement stok terjual
     */
    public function decrementSold(
        int $quantity = 1
    ): void {
        $this->decrement(
            'sold_quantity',
            $quantity
        );
    }

    /**
     * Increment total views
     */
    public function incrementViews(): void
    {
        $this->increment(
            'total_views'
        );
    }

    /**
     * Mengecek quantity masih valid
     */
    public function hasEnoughStock(
        int $quantity
    ): bool {
        return
            $this->remaining_stock >=
            $quantity;
    }

    /**
     * Mengecek user melewati limit pembelian
     */
    public function validatePurchaseLimit(
        int $quantity
    ): bool {
        return
            $quantity <=
            $this->max_purchase_per_user;
    }
}
