<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'shipments';

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
        | Provider Shipping
        |--------------------------------------------------------------------------
        */

        'provider',

        /*
        |--------------------------------------------------------------------------
        | Informasi Courier
        |--------------------------------------------------------------------------
        */

        'courier_name',
        'courier_service',

        /*
        |--------------------------------------------------------------------------
        | Informasi Resi
        |--------------------------------------------------------------------------
        */

        'tracking_number',
        'shipment_reference',

        /*
        |--------------------------------------------------------------------------
        | Status Pengiriman
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Catatan
        |--------------------------------------------------------------------------
        */

        'notes',

        /*
        |--------------------------------------------------------------------------
        | Payload Shipping API
        |--------------------------------------------------------------------------
        */

        'payload',

        /*
        |--------------------------------------------------------------------------
        | Timestamp Pengiriman
        |--------------------------------------------------------------------------
        */

        'shipped_at',
        'delivered_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // JSON payload
            'payload' => 'array',

            // Timestamp
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
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
     * Mengecek shipment sudah dikirim
     */
    public function getIsShippedAttribute(): bool
    {
        return in_array(
            $this->status,
            ['shipped', 'delivered']
        );
    }

    /**
     * Mengecek shipment sudah diterima
     */
    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === 'delivered';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark shipment sebagai shipped
     */
    public function markAsShipped(
        ?string $trackingNumber = null
    ): void {
        $this->update([

            'status' => 'shipped',

            'tracking_number' => $trackingNumber
                ?? $this->tracking_number,

            'shipped_at' => now(),
        ]);

        // Update status order otomatis
        $this->order?->update([
            'order_status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    /**
     * Mark shipment delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([

            'status' => 'delivered',

            'delivered_at' => now(),
        ]);

        // Update order otomatis
        $this->order?->update([
            'order_status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Cancel shipment
     */
    public function cancel(): void
    {
        $this->update([

            'status' => 'cancelled',
        ]);
    }
}
