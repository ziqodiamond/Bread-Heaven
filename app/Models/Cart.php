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

        // Relasi user
        'user_id',

        // Status cart
        'status',

        // Informasi cart
        'total_items',
        'total_quantity',
        'subtotal',

        // Expired cart
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

            // Total harga bigint
            'subtotal' => 'integer',

            // Timestamp
            'expired_at' => 'datetime',
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

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Recalculate subtotal cart
     */
    public function refreshCartSummary(): void
    {
        $items = $this->items;

        $this->update([

            'total_items' => $items->count(),

            'total_quantity' => $items->sum('quantity'),

            'subtotal' => $items->sum('subtotal'),
        ]);
    }
}
