<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';

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
        'user_address_id',
        'payment_method_id',

        /*
        |--------------------------------------------------------------------------
        | Invoice
        |--------------------------------------------------------------------------
        */

        'invoice_number',

        /*
        |--------------------------------------------------------------------------
        | Informasi Customer
        |--------------------------------------------------------------------------
        */

        'customer_name',
        'customer_email',
        'customer_phone',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Alamat Pengiriman
        |--------------------------------------------------------------------------
        */

        'shipping_receiver_name',
        'shipping_receiver_phone',

        'shipping_province',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',

        'shipping_full_address',
        'shipping_notes',

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        */

        'shipping_courier',
        'shipping_service',
        'shipping_etd',
        'tracking_number',

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Harga
        |--------------------------------------------------------------------------
        */

        'subtotal',
        'shipping_cost',
        'service_fee',
        'discount_amount',
        'grand_total',

        /*
        |--------------------------------------------------------------------------
        | Berat
        |--------------------------------------------------------------------------
        */

        'total_weight',

        /*
        |--------------------------------------------------------------------------
        | Payment Gateway
        |--------------------------------------------------------------------------
        */

        'payment_gateway',
        'payment_reference',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'order_status',
        'payment_status',

        /*
        |--------------------------------------------------------------------------
        | Catatan
        |--------------------------------------------------------------------------
        */

        'notes',

        /*
        |--------------------------------------------------------------------------
        | Timestamp Order
        |--------------------------------------------------------------------------
        */

        'paid_at',
        'shipped_at',
        'completed_at',
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
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'service_fee' => 'integer',
            'discount_amount' => 'integer',
            'grand_total' => 'integer',

            // Berat
            'total_weight' => 'integer',

            // Timestamp
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
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
     * Relasi ke alamat user
     */
    public function address()
    {
        return $this->belongsTo(
            UserAddress::class,
            'user_address_id'
        );
    }

    /**
     * Relasi ke payment method
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relasi item order
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi payment transaction
     */
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Relasi shipping rates
     */
    public function shippingRates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    /**
     * Relasi shipment
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Methods
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Auto generate invoice number
        |--------------------------------------------------------------------------
        */
        static::creating(function ($order) {

            if (!$order->invoice_number) {

                $order->invoice_number =
                    'INV-' .
                    now()->format('Ymd') .
                    '-' .
                    strtoupper(substr(uniqid(), -6));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek order sudah dibayar
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Mengecek order sudah dikirim
     */
    public function getIsShippedAttribute(): bool
    {
        return $this->order_status === 'shipped';
    }

    /**
     * Mengecek order selesai
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->order_status === 'completed';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark order sebagai paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'order_status'   => 'processing', // ✅ lebih proper
            'paid_at'        => now(),
        ]);
    }

    /**
     * Mark order sebagai shipped
     */
    public function markAsShipped(
        ?string $trackingNumber = null
    ): void {
        $this->update([

            'order_status' => 'shipped',

            'tracking_number' => $trackingNumber,

            'shipped_at' => now(),
        ]);
    }

    /**
     * Mark order selesai
     */
    public function markAsCompleted(): void
    {
        $this->update([

            'order_status' => 'completed',

            'completed_at' => now(),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(): void
    {
        $this->update([

            'order_status' => 'cancelled',
        ]);
    }
}
