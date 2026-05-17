<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'conversations';

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
        | Informasi Conversation
        |--------------------------------------------------------------------------
        */

        'type',
        'subject',

        /*
        |--------------------------------------------------------------------------
        | Relasi User
        |--------------------------------------------------------------------------
        */

        'customer_id',
        'admin_id',

        /*
        |--------------------------------------------------------------------------
        | Relasi Optional
        |--------------------------------------------------------------------------
        */

        'order_id',
        'product_id',

        /*
        |--------------------------------------------------------------------------
        | Last Message
        |--------------------------------------------------------------------------
        */

        'last_message',
        'last_message_at',

        /*
        |--------------------------------------------------------------------------
        | Unread Counter
        |--------------------------------------------------------------------------
        */

        'customer_unread_count',
        'admin_unread_count',

        /*
        |--------------------------------------------------------------------------
        | Status Conversation
        |--------------------------------------------------------------------------
        */

        'status',
        'priority',

        /*
        |--------------------------------------------------------------------------
        | Realtime Metadata
        |--------------------------------------------------------------------------
        */

        'customer_typing',
        'admin_typing',

        /*
        |--------------------------------------------------------------------------
        | Flags
        |--------------------------------------------------------------------------
        */

        'is_read_by_admin',
        'is_read_by_customer',
        'is_pinned',
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

            'customer_typing' => 'boolean',

            'admin_typing' => 'boolean',

            'is_read_by_admin' => 'boolean',

            'is_read_by_customer' => 'boolean',

            'is_pinned' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Integer
            |--------------------------------------------------------------------------
            */

            'customer_unread_count' => 'integer',

            'admin_unread_count' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'last_message_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi customer
     */
    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    /**
     * Relasi admin
     */
    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'admin_id'
        );
    }

    /**
     * Relasi order
     */
    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    /**
     * Relasi produk
     */
    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }

    /**
     * Relasi messages
     */
    public function messages()
    {
        return $this->hasMany(
            Message::class
        );
    }

    /**
     * Last message realtime
     */
    public function latestMessage()
    {
        return $this->hasOne(
            Message::class
        )->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek conversation masih aktif
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Mengecek conversation selesai
     */
    public function getIsResolvedAttribute(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Mengecek conversation closed
     */
    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Mengecek ada unread admin
     */
    public function getHasUnreadAdminAttribute(): bool
    {
        return
            $this->admin_unread_count > 0;
    }

    /**
     * Mengecek ada unread customer
     */
    public function getHasUnreadCustomerAttribute(): bool
    {
        return
            $this->customer_unread_count > 0;
    }

    /**
     * Mengecek customer sedang mengetik
     */
    public function getCustomerIsTypingAttribute(): bool
    {
        return $this->customer_typing;
    }

    /**
     * Mengecek admin sedang mengetik
     */
    public function getAdminIsTypingAttribute(): bool
    {
        return $this->admin_typing;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Conversation open
     */
    public function scopeOpen($query)
    {
        return $query->where(
            'status',
            'open'
        );
    }

    /**
     * Conversation prioritas tinggi
     */
    public function scopePriority($query)
    {
        return $query->whereIn(
            'priority',
            ['high', 'urgent']
        );
    }

    /**
     * Conversation customer
     */
    public function scopeCustomer(
        $query,
        string $customerId
    ) {
        return $query->where(
            'customer_id',
            $customerId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark read admin
     */
    public function markAsReadByAdmin(): void
    {
        $this->update([

            'is_read_by_admin' => true,

            'admin_unread_count' => 0,
        ]);
    }

    /**
     * Mark read customer
     */
    public function markAsReadByCustomer(): void
    {
        $this->update([

            'is_read_by_customer' => true,

            'customer_unread_count' => 0,
        ]);
    }

    /**
     * Mark resolved
     */
    public function resolve(): void
    {
        $this->update([

            'status' => 'resolved',
        ]);
    }

    /**
     * Close conversation
     */
    public function close(): void
    {
        $this->update([

            'status' => 'closed',
        ]);
    }

    /**
     * Reopen conversation
     */
    public function reopen(): void
    {
        $this->update([

            'status' => 'open',
        ]);
    }

    /**
     * Update last message
     */
    public function updateLastMessage(
        string $message
    ): void {
        $this->update([

            'last_message' => $message,

            'last_message_at' => now(),
        ]);
    }
}
