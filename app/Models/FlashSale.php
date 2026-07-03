<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashSale extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'flash_sales';

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
        | Informasi Flash Sale
        |--------------------------------------------------------------------------
        */

        'name',
        'slug',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Banner & Thumbnail
        |--------------------------------------------------------------------------
        */

        'banner',
        'thumbnail',

        /*
        |--------------------------------------------------------------------------
        | Tampilan Promo
        |--------------------------------------------------------------------------
        */

        'label',
        'badge_color',

        /*
        |--------------------------------------------------------------------------
        | Jadwal Flash Sale
        |--------------------------------------------------------------------------
        */

        'start_at',
        'end_at',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'status',
        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Pengaturan Flash Sale
        |--------------------------------------------------------------------------
        */

        'show_countdown',
        'show_in_homepage',
        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        'total_views',
        'total_orders',
        'total_items_sold',

        /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

        'meta_title',
        'meta_description',
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
            | Boolean
            |--------------------------------------------------------------------------
            */

            'is_active' => 'boolean',

            'show_countdown' => 'boolean',

            'show_in_homepage' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'start_at' => 'datetime',

            'end_at' => 'datetime',

            /*
            |--------------------------------------------------------------------------
            | Integer
            |--------------------------------------------------------------------------
            */

            'sort_order' => 'integer',

            'total_views' => 'integer',

            'total_orders' => 'integer',

            'total_items_sold' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi item flash sale
     */
    public function items()
    {
        return $this->hasMany(
            FlashSaleItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek flash sale sudah dimulai
     */
    public function getHasStartedAttribute(): bool
    {
        return now()->gte(
            $this->start_at
        );
    }

    /**
     * Mengecek flash sale sudah selesai
     */
    public function getHasEndedAttribute(): bool
    {
        return now()->gt(
            $this->end_at
        );
    }

    /**
     * Mengecek flash sale aktif
     */
    public function getIsRunningAttribute(): bool
    {
        return
            $this->is_active &&
            !$this->has_ended &&
            $this->has_started;
    }

    /**
     * Countdown flash sale
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->is_running) {
            return 0;
        }

        return now()->diffInSeconds(
            $this->end_at,
            false
        );
    }

    /**
     * URL banner
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner
            ? asset(
                'storage/' . $this->banner
            )
            : null;
    }

    /**
     * URL thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail
            ? asset(
                'storage/' . $this->thumbnail
            )
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Flash sale aktif
     */
    public function scopeActive($query)
    {
        return $query

            ->where(
                'is_active',
                true
            )

            ->whereIn('status', [
                'active',
                'scheduled',
            ]);
    }

    /**
     * Flash sale berjalan
     */
    public function scopeRunning($query)
    {
        return $query

            ->active()

            ->where(
                'start_at',
                '<=',
                now()
            )

            ->where(
                'end_at',
                '>=',
                now()
            );
    }

    /**
     * Tampilkan di homepage
     */
    public function scopeHomepage($query)
    {
        return $query->where(
            'show_in_homepage',
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

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
     * Increment total order
     */
    public function incrementOrders(
        int $count = 1
    ): void {
        $this->increment(
            'total_orders',
            $count
        );
    }

    /**
     * Increment total item sold
     */
    public function incrementItemsSold(
        int $count = 1
    ): void {
        $this->increment(
            'total_items_sold',
            $count
        );
    }

    /**
     * Update status otomatis
     */
    public function refreshStatus(): void
    {
        if (!$this->is_active) {

            $this->update([
                'status' => 'cancelled',
            ]);

            return;
        }

        if (now()->lt($this->start_at)) {

            $this->update([
                'status' => 'scheduled',
            ]);

            return;
        }

        if (now()->between(
            $this->start_at,
            $this->end_at
        )) {

            $this->update([
                'status' => 'active',
            ]);

            return;
        }

        $this->update([
            'status' => 'expired',
            'show_in_homepage' => false,
        ]);
    }
}
