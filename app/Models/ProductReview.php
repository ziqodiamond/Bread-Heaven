<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReview extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'product_reviews';

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

        'user_id',
        'order_id',
        'order_item_id',
        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Rating & Review
        |--------------------------------------------------------------------------
        */

        'rating',
        'review',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_anonymous',
        'is_visible',

        /*
        |--------------------------------------------------------------------------
        | Admin Reply
        |--------------------------------------------------------------------------
        */

        'admin_reply',
        'admin_replied_at',

        /*
        |--------------------------------------------------------------------------
        | Moderation
        |--------------------------------------------------------------------------
        */

        'hidden_at',
        'hidden_reason',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // Rating
            'rating' => 'integer',

            // Boolean
            'is_anonymous' => 'boolean',
            'is_visible' => 'boolean',

            // Timestamp
            'admin_replied_at' => 'datetime',
            'hidden_at' => 'datetime',
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
     * Relasi ke order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke order item
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Relasi ke product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi media review
     */
    public function media()
    {
        return $this->hasMany(
            ProductReviewMedia::class
        )->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek review dibalas admin
     */
    public function getHasAdminReplyAttribute(): bool
    {
        return !empty($this->admin_reply);
    }

    /**
     * Mengecek review disembunyikan
     */
    public function getIsHiddenAttribute(): bool
    {
        return !$this->is_visible;
    }

    /**
     * Total media review
     */
    public function getMediaCountAttribute(): int
    {
        return $this->media()->count();
    }

    /**
     * Mengecek review memiliki media
     */
    public function getHasMediaAttribute(): bool
    {
        return $this->media()->exists();
    }

    /**
     * Mengecek review memiliki image
     */
    public function getHasImageAttribute(): bool
    {
        return $this->media()
            ->where('type', 'image')
            ->exists();
    }

    /**
     * Mengecek review memiliki video
     */
    public function getHasVideoAttribute(): bool
    {
        return $this->media()
            ->where('type', 'video')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Review visible
     */
    public function scopeVisible($query)
    {
        return $query->where(
            'is_visible',
            true
        );
    }

    /**
     * Filter rating
     */
    public function scopeRating(
        $query,
        int $rating
    ) {
        return $query->where(
            'rating',
            $rating
        );
    }

    /**
     * Review dengan media
     */
    public function scopeWithMedia($query)
    {
        return $query->whereHas('media');
    }

    /**
     * Review dengan gambar
     */
    public function scopeWithImage($query)
    {
        return $query->whereHas(
            'media',
            fn($q) => $q->where(
                'type',
                'image'
            )
        );
    }

    /**
     * Review dengan video
     */
    public function scopeWithVideo($query)
    {
        return $query->whereHas(
            'media',
            fn($q) => $q->where(
                'type',
                'video'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Sembunyikan review
     */
    public function hide(
        ?string $reason = null
    ): void {
        $this->update([

            'is_visible' => false,

            'hidden_at' => now(),

            'hidden_reason' => $reason,
        ]);
    }

    /**
     * Tampilkan kembali review
     */
    public function show(): void
    {
        $this->update([

            'is_visible' => true,

            'hidden_at' => null,

            'hidden_reason' => null,
        ]);
    }

    /**
     * Balas review sebagai admin
     */
    public function reply(
        string $message
    ): void {
        $this->update([

            'admin_reply' => $message,

            'admin_replied_at' => now(),
        ]);
    }
}
