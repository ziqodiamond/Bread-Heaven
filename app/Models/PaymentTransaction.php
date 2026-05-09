<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'payment_transactions';

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

        'order_id',

        /*
        |--------------------------------------------------------------------------
        | Gateway
        |--------------------------------------------------------------------------
        */

        'gateway',
        'gateway_transaction_id',
        'gateway_order_id',

        /*
        |--------------------------------------------------------------------------
        | Informasi Payment
        |--------------------------------------------------------------------------
        */

        'payment_type',
        'bank',

        'va_number',

        'bill_key',
        'biller_code',

        /*
        |--------------------------------------------------------------------------
        | Harga
        |--------------------------------------------------------------------------
        */

        'gross_amount',
        'currency',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'transaction_status',
        'fraud_status',

        /*
        |--------------------------------------------------------------------------
        | URL Payment
        |--------------------------------------------------------------------------
        */

        'snap_token',
        'payment_url',

        /*
        |--------------------------------------------------------------------------
        | Expired
        |--------------------------------------------------------------------------
        */

        'expired_at',

        /*
        |--------------------------------------------------------------------------
        | Payload Gateway
        |--------------------------------------------------------------------------
        */

        'payload',

        /*
        |--------------------------------------------------------------------------
        | Timestamp Payment
        |--------------------------------------------------------------------------
        */

        'paid_at',
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
            'gross_amount' => 'integer',

            // JSON payload
            'payload' => 'array',

            // Timestamp
            'expired_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek transaksi berhasil
     */
    public function getIsPaidAttribute(): bool
    {
        return in_array(
            $this->transaction_status,
            ['settlement', 'capture', 'paid']
        );
    }

    /**
     * Mengecek transaksi pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Mengecek transaksi expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return in_array(
            $this->transaction_status,
            ['expire', 'expired']
        );
    }

    /**
     * Mengecek transaksi gagal
     */
    public function getIsFailedAttribute(): bool
    {
        return in_array(
            $this->transaction_status,
            ['deny', 'cancel', 'failed']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark transaction sebagai paid
     */
    public function markAsPaid(
        array $payload = []
    ): void {
        $this->update([

            'transaction_status' => 'settlement',

            'payload' => $payload,

            'paid_at' => now(),
        ]);

        // Update order otomatis
        $this->order?->markAsPaid();
    }

    /**
     * Mark transaction expired
     */
    public function markAsExpired(
        array $payload = []
    ): void {
        $this->update([

            'transaction_status' => 'expire',

            'payload' => $payload,
        ]);

        // Update order otomatis
        $this->order?->update([
            'payment_status' => 'expired',
        ]);
    }

    /**
     * Mark transaction failed
     */
    public function markAsFailed(
        array $payload = []
    ): void {
        $this->update([

            'transaction_status' => 'failed',

            'payload' => $payload,
        ]);

        // Update order otomatis
        $this->order?->update([
            'payment_status' => 'failed',
        ]);
    }
}
