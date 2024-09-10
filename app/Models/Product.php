<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'stock',
        'status',
        'image_url',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi dengan CartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
