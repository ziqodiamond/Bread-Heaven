<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'product_images';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'product_id',

        'image_url',

        'alt_text',

        'sort_order',

        'is_primary',

        'is_active',
    ];

    protected function casts(): array
    {
        return [

            'sort_order' => 'integer',

            'is_primary' => 'boolean',

            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Full image URL
     */
    public function getImageAttribute(): string
    {
        return asset('storage/' . $this->image_url);
    }
}
