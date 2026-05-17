<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'products';

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
        | Informasi Produk
        |--------------------------------------------------------------------------
        */

        'name',
        'slug',
        'sku',
        'category',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Harga & Discount
        |--------------------------------------------------------------------------
        */

        // Harga asli
        'price',

        // Harga diskon
        'sale_price',

        // Jadwal diskon
        'discount_start_at',
        'discount_end_at',

        // Informasi discount
        'discount_label',
        'discount_type',
        'discount_value',

        // Stok
        'stock',

        /*
        |--------------------------------------------------------------------------
        | Informasi Shipping
        |--------------------------------------------------------------------------
        */

        'weight',
        'length',
        'width',
        'height',

        /*
        |--------------------------------------------------------------------------
        | Status
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
            | Harga
            |--------------------------------------------------------------------------
            */

            'price' => 'integer',
            'sale_price' => 'integer',
            'discount_value' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Jadwal Discount
            |--------------------------------------------------------------------------
            */

            'discount_start_at' => 'datetime',
            'discount_end_at' => 'datetime',

            /*
            |--------------------------------------------------------------------------
            | Stok
            |--------------------------------------------------------------------------
            */

            'stock' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Berat & Dimensi
            |--------------------------------------------------------------------------
            */

            'weight' => 'integer',
            'length' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi item cart
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relasi item order
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi multiple image produk
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    /**
     * Relasi thumbnail utama
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek stok tersedia
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Mengecek produk tersedia
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Mengecek apakah discount aktif
     */
    public function getHasActiveDiscountAttribute(): bool
    {
        // Tidak ada harga diskon
        if (!$this->sale_price) {
            return false;
        }

        // Jadwal mulai discount
        if (
            $this->discount_start_at &&
            now()->lt($this->discount_start_at)
        ) {
            return false;
        }

        // Jadwal selesai discount
        if (
            $this->discount_end_at &&
            now()->gt($this->discount_end_at)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Harga final produk
     */
    public function getFinalPriceAttribute(): int
    {
        if ($this->has_active_discount) {
            return $this->sale_price;
        }

        return $this->price;
    }

    /**
     * Jumlah discount produk
     */
    public function getDiscountAmountAttribute(): int
    {
        if (!$this->has_active_discount) {
            return 0;
        }

        return $this->price - $this->sale_price;
    }

    /**
     * Persentase discount produk
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (
            !$this->has_active_discount ||
            $this->price <= 0
        ) {
            return 0;
        }

        return (int) round(
            (
                $this->discount_amount / $this->price
            ) * 100
        );
    }

    /**
     * Mengecek produk flash sale
     */
    public function getIsFlashSaleAttribute(): bool
    {
        return
            $this->has_active_discount &&
            str_contains(
                strtolower($this->discount_label ?? ''),
                'flash'
            );
    }

    /**
     * URL thumbnail default
     */
    public function getImageAttribute(): string
    {
        return $this->image_url
            ? asset('storage/' . $this->image_url)
            : asset('images/no-image.png');
    }

    /**
     * Thumbnail utama produk
     */
    public function getThumbnailAttribute(): string
    {
        $primaryImage = $this->primaryImage;

        if ($primaryImage) {
            return asset(
                'storage/' . $primaryImage->image_url
            );
        }

        return asset('images/no-image.png');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Produk available
     */
    public function scopeAvailable($query)
    {
        return $query->where(
            'status',
            'available'
        );
    }

    /**
     * Produk masih ada stok
     */
    public function scopeInStock($query)
    {
        return $query->where(
            'stock',
            '>',
            0
        );
    }

    /**
     * Filter kategori
     */
    public function scopeCategory(
        $query,
        string $category
    ) {
        return $query->where(
            'category',
            $category
        );
    }

    /**
     * Produk yang sedang discount
     */
    public function scopeDiscountActive($query)
    {
        return $query

            ->whereNotNull('sale_price')

            ->where(function ($query) {

                $query

                    ->whereNull('discount_start_at')

                    ->orWhere(
                        'discount_start_at',
                        '<=',
                        now()
                    );
            })

            ->where(function ($query) {

                $query

                    ->whereNull('discount_end_at')

                    ->orWhere(
                        'discount_end_at',
                        '>=',
                        now()
                    );
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mengurangi stok produk
     */
    public function decreaseStock(
        int $quantity
    ): void {
        $this->decrement(
            'stock',
            $quantity
        );
    }

    /**
     * Menambah stok produk
     */
    public function increaseStock(
        int $quantity
    ): void {
        $this->increment(
            'stock',
            $quantity
        );
    }

    /**
     * Mengecek apakah stok cukup
     */
    public function hasEnoughStock(
        int $quantity
    ): bool {
        return $this->stock >= $quantity;
    }
}
