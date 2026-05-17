<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageAttachment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'message_attachments';

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

        'message_id',

        /*
        |--------------------------------------------------------------------------
        | Informasi File
        |--------------------------------------------------------------------------
        */

        'file_name',
        'stored_file_name',
        'file_path',

        /*
        |--------------------------------------------------------------------------
        | Metadata File
        |--------------------------------------------------------------------------
        */

        'mime_type',
        'extension',
        'file_size',

        /*
        |--------------------------------------------------------------------------
        | Tipe Attachment
        |--------------------------------------------------------------------------
        */

        'type',

        /*
        |--------------------------------------------------------------------------
        | Metadata Media
        |--------------------------------------------------------------------------
        */

        'width',
        'height',
        'duration',

        /*
        |--------------------------------------------------------------------------
        | Flags
        |--------------------------------------------------------------------------
        */

        'is_deleted',
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
            | Integer
            |--------------------------------------------------------------------------
            */

            'file_size' => 'integer',

            'width' => 'integer',

            'height' => 'integer',

            'duration' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            'is_deleted' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi message
     */
    public function message()
    {
        return $this->belongsTo(
            Message::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * URL file attachment
     */
    public function getUrlAttribute(): string
    {
        return asset(
            'storage/' .
                $this->file_path
        );
    }

    /**
     * Mengecek attachment image
     */
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Mengecek attachment video
     */
    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Mengecek attachment audio
     */
    public function getIsAudioAttribute(): bool
    {
        return $this->type === 'audio';
    }

    /**
     * Mengecek attachment file
     */
    public function getIsFileAttribute(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Format ukuran file
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {

            return number_format(
                $bytes / 1073741824,
                2
            ) . ' GB';
        }

        if ($bytes >= 1048576) {

            return number_format(
                $bytes / 1048576,
                2
            ) . ' MB';
        }

        if ($bytes >= 1024) {

            return number_format(
                $bytes / 1024,
                2
            ) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Thumbnail attachment
     */
    public function getThumbnailAttribute(): ?string
    {
        if (!$this->is_image) {
            return null;
        }

        return $this->url;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Attachment image
     */
    public function scopeImages($query)
    {
        return $query->where(
            'type',
            'image'
        );
    }

    /**
     * Attachment video
     */
    public function scopeVideos($query)
    {
        return $query->where(
            'type',
            'video'
        );
    }

    /**
     * Attachment file
     */
    public function scopeFiles($query)
    {
        return $query->where(
            'type',
            'file'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Soft delete attachment
     */
    public function softDeleteAttachment(): void
    {
        $this->update([

            'is_deleted' => true,
        ]);
    }

    /**
     * Mendapatkan extension uppercase
     */
    public function extensionUpper(): string
    {
        return strtoupper(
            $this->extension ?? ''
        );
    }
}
