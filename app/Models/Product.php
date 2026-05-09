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
        | Harga & Stok
        |--------------------------------------------------------------------------
        */

        'price',
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

            // Harga bigint
            'price' => 'integer',

            // Stok
            'stock' => 'integer',

            // Berat & dimensi
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
            return asset('storage/' . $primaryImage->image_url);
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
        return $query->where('status', 'available');
    }

    /**
     * Produk masih ada stok
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Filter kategori
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mengurangi stok produk
     */
    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Menambah stok produk
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Mengecek apakah stok cukup
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
