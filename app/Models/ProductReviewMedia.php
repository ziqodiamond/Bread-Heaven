<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReviewMedia extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'product_review_media';

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

        'product_review_id',

        /*
        |--------------------------------------------------------------------------
        | Media
        |--------------------------------------------------------------------------
        */

        'type',
        'file_url',

        /*
        |--------------------------------------------------------------------------
        | Informasi File
        |--------------------------------------------------------------------------
        */

        'mime_type',
        'file_size',

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // File size
            'file_size' => 'integer',

            // Sorting
            'sort_order' => 'integer',

            // Boolean
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke review
     */
    public function review()
    {
        return $this->belongsTo(
            ProductReview::class,
            'product_review_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Full URL media
     */
    public function getFileAttribute(): string
    {
        return asset('storage/' . $this->file_url);
    }

    /**
     * Mengecek media image
     */
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Mengecek media video
     */
    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Format ukuran file
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Media aktif
     */
    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

    /**
     * Filter image
     */
    public function scopeImage($query)
    {
        return $query->where(
            'type',
            'image'
        );
    }

    /**
     * Filter video
     */
    public function scopeVideo($query)
    {
        return $query->where(
            'type',
            'video'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Nonaktifkan media
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    /**
     * Aktifkan media
     */
    public function activate(): void
    {
        $this->update([
            'is_active' => true,
        ]);
    }
}
