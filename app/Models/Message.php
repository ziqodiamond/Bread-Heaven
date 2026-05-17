<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'messages';

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

        'conversation_id',
        'sender_id',

        /*
        |--------------------------------------------------------------------------
        | Isi Message
        |--------------------------------------------------------------------------
        */

        'message',

        /*
        |--------------------------------------------------------------------------
        | Tipe Message
        |--------------------------------------------------------------------------
        */

        'type',

        /*
        |--------------------------------------------------------------------------
        | Reply Message
        |--------------------------------------------------------------------------
        */

        'reply_to_id',

        /*
        |--------------------------------------------------------------------------
        | Metadata Message
        |--------------------------------------------------------------------------
        */

        'is_read',
        'is_edited',

        /*
        |--------------------------------------------------------------------------
        | Timestamp Metadata
        |--------------------------------------------------------------------------
        */

        'read_at',
        'edited_at',

        /*
        |--------------------------------------------------------------------------
        | Realtime Metadata
        |--------------------------------------------------------------------------
        */

        'client_id',
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

            'is_read' => 'boolean',

            'is_edited' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'read_at' => 'datetime',

            'edited_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi conversation
     */
    public function conversation()
    {
        return $this->belongsTo(
            Conversation::class
        );
    }

    /**
     * Relasi sender
     */
    public function sender()
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }

    /**
     * Reply message
     */
    public function replyTo()
    {
        return $this->belongsTo(
            Message::class,
            'reply_to_id'
        );
    }

    /**
     * Child replies
     */
    public function replies()
    {
        return $this->hasMany(
            Message::class,
            'reply_to_id'
        );
    }

    /**
     * Attachment message
     */
    public function attachments()
    {
        return $this->hasMany(
            MessageAttachment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek message text
     */
    public function getIsTextAttribute(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Mengecek message image
     */
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Mengecek message file
     */
    public function getIsFileAttribute(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Mengecek message system
     */
    public function getIsSystemAttribute(): bool
    {
        return $this->type === 'system';
    }

    /**
     * Mengecek message sudah dibaca
     */
    public function getHasBeenReadAttribute(): bool
    {
        return $this->is_read;
    }

    /**
     * Mengecek message edited
     */
    public function getHasBeenEditedAttribute(): bool
    {
        return $this->is_edited;
    }

    /**
     * Mengecek message deleted
     */
    public function getHasBeenDeletedAttribute(): bool
    {
        return $this->trashed();
    }

    /**
     * Preview message
     */
    public function getPreviewAttribute(): string
    {
        if ($this->trashed()) {
            return 'Pesan dihapus';
        }

        if ($this->type === 'image') {
            return '📷 Gambar';
        }

        if ($this->type === 'file') {
            return '📁 File';
        }

        return str(
            $this->message
        )->limit(60);
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
        | Update last message conversation
        |--------------------------------------------------------------------------
        */
        static::created(function ($message) {

            $conversation =
                $message->conversation;

            if (!$conversation) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Update last message
            |--------------------------------------------------------------------------
            */

            $conversation->update([

                'last_message' =>
                $message->preview,

                'last_message_at' =>
                now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Increment unread counter
            |--------------------------------------------------------------------------
            */

            // Message dari customer
            if (
                $conversation->customer_id ===
                $message->sender_id
            ) {

                $conversation->increment(
                    'admin_unread_count'
                );

                $conversation->update([

                    'is_read_by_admin' => false,
                ]);
            }

            // Message dari admin
            else {

                $conversation->increment(
                    'customer_unread_count'
                );

                $conversation->update([

                    'is_read_by_customer' => false,
                ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Message unread
     */
    public function scopeUnread($query)
    {
        return $query->where(
            'is_read',
            false
        );
    }

    /**
     * Message text
     */
    public function scopeText($query)
    {
        return $query->where(
            'type',
            'text'
        );
    }

    /**
     * Message image
     */
    public function scopeImage($query)
    {
        return $query->where(
            'type',
            'image'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark message read
     */
    public function markAsRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->update([

            'is_read' => true,

            'read_at' => now(),
        ]);
    }

    /**
     * Edit message
     */
    public function edit(
        string $message
    ): void {
        $this->update([

            'message' => $message,

            'is_edited' => true,

            'edited_at' => now(),
        ]);
    }

    /**
     * Soft delete message
     */
    public function softDeleteMessage(): void
    {
        $this->update([

            'message' => null,
        ]);

        $this->delete();
    }
}
