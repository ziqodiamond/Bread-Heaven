<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'payment_method',   // Added payment_method
        'delivery_method',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'total_price',
        'status',
        'payment_status',
    ];

    /**
     * Relasi dengan model TransactionDetail.
     * Setiap transaksi memiliki banyak detail transaksi.
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Relasi dengan model User.
     * Setiap transaksi terkait dengan satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan model PaymentMethod.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method');
    }

    /**
     * Relasi dengan model DeliveryMethod.
     */
    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method');
    }
}
