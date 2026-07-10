<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'category_id',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Harga & Discount
        |--------------------------------------------------------------------------
        */

        // Harga normal produk
        'price',

        // Harga setelah discount
        'sale_price',

        // Jadwal discount produk
        'discount_start_at',
        'discount_end_at',

        // Informasi discount
        'discount_label',
        'discount_type',
        'discount_value',
        'discount_max',

        /*
        |--------------------------------------------------------------------------
        | Stock
        |--------------------------------------------------------------------------
        */

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
        | Status Produk
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

            'discount_value' => 'decimal:2',

            'discount_max' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Jadwal Discount
            |--------------------------------------------------------------------------
            */

            'discount_start_at' => 'datetime',

            'discount_end_at' => 'datetime',

            /*
            |--------------------------------------------------------------------------
            | Stock
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
     * Relasi kategori
     *
     * Cara pakai:
     * $product->category
     */
    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id'
        );
    }

    /**
     * Relasi item cart
     *
     * Cara pakai:
     * $product->cartItems
     */
    public function cartItems()
    {
        return $this->hasMany(
            CartItem::class
        );
    }

    /**
     * Relasi item order
     *
     * Cara pakai:
     * $product->orderItems
     */
    public function orderItems()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    /**
     * Relasi multiple gambar produk
     *
     * Cara pakai:
     * $product->images
     */
    public function images()
    {
        return $this->hasMany(
            ProductImage::class
        )->orderBy('sort_order');
    }

    /**
     * Relasi thumbnail utama produk
     *
     * Cara pakai:
     * $product->primaryImage
     */
    public function primaryImage()
    {
        return $this->hasOne(
            ProductImage::class
        )->where(
            'is_primary',
            true
        );
    }

    /**
     * Relasi flash sale item aktif
     *
     * Digunakan untuk mengambil
     * flash sale yang sedang berjalan
     * untuk produk ini.
     *
     * Cara pakai:
     * $product->activeFlashSaleItem
     */
    public function activeFlashSaleItem()
    {
        return $this->hasOne(
            FlashSaleItem::class
        )

            ->whereHas(
                'flashSale',
                function ($query) {

                    $query

                        ->where(
                            'start_at',
                            '<=',
                            now()
                        )

                        ->where(
                            'end_at',
                            '>=',
                            now()
                        )

                        ->where(
                            'is_active',
                            true
                        );
                }
            )

            ->where(
                'is_active',
                true
            )

            ->whereRaw(
                'sold_quantity < stock_limit'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek stok tersedia
     *
     * Return:
     * true / false
     *
     * Cara pakai:
     * $product->in_stock
     */
    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Mengecek produk tersedia
     *
     * Return:
     * true / false
     *
     * Cara pakai:
     * $product->is_available
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Mengecek apakah product discount aktif
     *
     * Akan mengecek:
     * - sale_price tersedia
     * - jadwal discount dimulai
     * - jadwal discount belum selesai
     *
     * Cara pakai:
     * $product->has_active_discount
     */
    public function getHasActiveDiscountAttribute(): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Tidak ada harga discount
        |--------------------------------------------------------------------------
        */

        if (!$this->sale_price) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Discount belum dimulai
        |--------------------------------------------------------------------------
        */

        if (
            $this->discount_start_at &&
            now()->lt($this->discount_start_at)
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Discount sudah selesai
        |--------------------------------------------------------------------------
        */

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
     *
     * Prioritas:
     * - Harga discount
     * - Harga normal
     *
     * Cara pakai:
     * $product->final_price
     */
    public function getFinalPriceAttribute(): int
    {
        if ($this->has_active_discount) {
            return $this->sale_price;
        }

        return $this->price;
    }

    /**
     * Total potongan discount produk
     *
     * Contoh:
     * 100000 - 80000 = 20000
     *
     * Cara pakai:
     * $product->discount_amount
     */
    public function getDiscountAmountAttribute(): int
    {
        if (!$this->has_active_discount) {
            return 0;
        }

        return
            $this->price -
            $this->sale_price;
    }

    /**
     * Persentase discount produk
     *
     * Contoh:
     * 20 (%)
     *
     * Cara pakai:
     * $product->discount_percentage
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
                $this->discount_amount /
                $this->price
            ) * 100
        );
    }

    /**
     * Mengecek produk sedang flash sale
     *
     * Cara pakai:
     * $product->is_flash_sale
     */
    public function getIsFlashSaleAttribute(): bool
    {
        return
            $this->activeFlashSaleItem !== null;
    }

    /**
     * Thumbnail utama produk
     *
     * Fallback ke gambar default
     * jika thumbnail utama kosong.
     *
     * Cara pakai:
     * $product->thumbnail
     */
    public function getThumbnailAttribute(): string
    {
        $primaryImage =
            $this->primaryImage;

        if ($primaryImage) {

            return asset(

                'storage/' .

                    $primaryImage->image_url
            );
        }

        return asset(
            'images/no-image.png'
        );
    }

    /**
     * Harga produk yang sudah resolve otomatis
     *
     * Prioritas harga:
     * 1. Flash Sale
     * 2. Product Discount
     * 3. Harga Normal
     *
     * Cara pakai:
     * $product->resolved_price
     */
    public function getResolvedPriceAttribute(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Prioritas Flash Sale
        |--------------------------------------------------------------------------
        */

        if ($this->activeFlashSaleItem) {

            return $this

                ->activeFlashSaleItem

                ->sale_price;
        }

        /*
        |--------------------------------------------------------------------------
        | Product Discount
        |--------------------------------------------------------------------------
        */

        if ($this->has_active_discount) {

            return $this->sale_price;
        }

        /*
        |--------------------------------------------------------------------------
        | Harga Normal
        |--------------------------------------------------------------------------
        */

        return $this->price;
    }

    /**
     * Tipe discount aktif
     *
     * Berguna untuk badge UI frontend.
     *
     * Return:
     * - flash_sale
     * - product_discount
     * - none
     *
     * Cara pakai:
     * $product->active_discount_type
     */
    public function getActiveDiscountTypeAttribute(): string
    {
        /*
        |--------------------------------------------------------------------------
        | Flash Sale
        |--------------------------------------------------------------------------
        */

        if ($this->activeFlashSaleItem) {
            return 'flash_sale';
        }

        /*
        |--------------------------------------------------------------------------
        | Product Discount
        |--------------------------------------------------------------------------
        */

        if ($this->has_active_discount) {
            return 'product_discount';
        }

        /*
        |--------------------------------------------------------------------------
        | Tidak ada discount
        |--------------------------------------------------------------------------
        */

        return 'none';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Produk available
     *
     * Cara pakai:
     * Product::available()->get();
     */
    public function scopeAvailable($query)
    {
        return $query->where(
            'status',
            'available'
        );
    }

    /**
     * Produk masih memiliki stok
     *
     * Cara pakai:
     * Product::inStock()->get();
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
     * Filter berdasarkan kategori
     *
     * Cara pakai:
     * Product::category(1)->get();
     */
    public function scopeCategory(
        $query,
        $category
    ) {
        return $query->where(
            'category_id',
            $category
        );
    }

    /**
     * Produk yang sedang discount
     *
     * Cara pakai:
     * Product::discountActive()->get();
     */
    public function scopeDiscountActive($query)
    {
        return $query

            ->whereNotNull(
                'sale_price'
            )

            ->where(function ($query) {

                $query

                    ->whereNull(
                        'discount_start_at'
                    )

                    ->orWhere(
                        'discount_start_at',
                        '<=',
                        now()
                    );
            })

            ->where(function ($query) {

                $query

                    ->whereNull(
                        'discount_end_at'
                    )

                    ->orWhere(
                        'discount_end_at',
                        '>=',
                        now()
                    );
            });
    }

    /**
     * Produk yang sedang flash sale aktif
     *
     * Cara pakai:
     * Product::flashSaleActive()->get();
     */
    public function scopeFlashSaleActive($query)
    {
        return $query

            ->whereHas(
                'activeFlashSaleItem',
                function ($subQuery) {
                    $subQuery

                        ->where('is_active', true)

                        ->whereRaw(
                            'sold_quantity < stock_limit'
                        );
                }
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mengurangi stok produk
     *
     * Contoh:
     * $product->decreaseStock(2);
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
     *
     * Contoh:
     * $product->increaseStock(5);
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
     * Mengecek stok produk cukup
     *
     * Contoh:
     * $product->hasEnoughStock(3);
     */
    public function hasEnoughStock(
        int $quantity
    ): bool {
        return
            $this->stock >=
            $quantity;
    }
}
