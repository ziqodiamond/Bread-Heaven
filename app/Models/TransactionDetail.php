<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    /**
     * Relasi dengan model Transaction.
     * Setiap detail transaksi milik satu transaksi.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi dengan model Product.
     * Setiap detail transaksi berkaitan dengan satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Hitung subtotal secara otomatis jika perlu.
     * (Jika ingin menghitung subtotal di dalam model)
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }
}
