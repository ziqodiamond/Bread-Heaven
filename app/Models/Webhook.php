<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Webhook extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'webhooks';

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
        | Provider Webhook
        |--------------------------------------------------------------------------
        */

        'provider',

        /*
        |--------------------------------------------------------------------------
        | Event Webhook
        |--------------------------------------------------------------------------
        */

        'event_type',

        /*
        |--------------------------------------------------------------------------
        | Reference
        |--------------------------------------------------------------------------
        */

        'reference_id',

        /*
        |--------------------------------------------------------------------------
        | Status Processing
        |--------------------------------------------------------------------------
        */

        'processed',
        'processed_at',

        /*
        |--------------------------------------------------------------------------
        | Informasi Request
        |--------------------------------------------------------------------------
        */

        'method',
        'signature',
        'ip_address',

        /*
        |--------------------------------------------------------------------------
        | Payload
        |--------------------------------------------------------------------------
        */

        'payload',

        /*
        |--------------------------------------------------------------------------
        | Error Handling
        |--------------------------------------------------------------------------
        */

        'error_message',
        'retry_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            // Boolean
            'processed' => 'boolean',

            // JSON payload
            'payload' => 'array',

            // Retry
            'retry_count' => 'integer',

            // Timestamp
            'processed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek webhook sudah diproses
     */
    public function getIsProcessedAttribute(): bool
    {
        return $this->processed === true;
    }

    /**
     * Mengecek webhook gagal diproses
     */
    public function getHasErrorAttribute(): bool
    {
        return !empty($this->error_message);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Webhook belum diproses
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    /**
     * Filter provider
     */
    public function scopeProvider(
        $query,
        string $provider
    ) {
        return $query->where(
            'provider',
            $provider
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark webhook berhasil diproses
     */
    public function markAsProcessed(): void
    {
        $this->update([

            'processed' => true,

            'processed_at' => now(),

            'error_message' => null,
        ]);
    }

    /**
     * Mark webhook gagal diproses
     */
    public function markAsFailed(
        string $message
    ): void {
        $this->increment('retry_count');

        $this->update([

            'processed' => false,

            'error_message' => $message,
        ]);
    }
}
