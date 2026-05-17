<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory, HasUuids;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'vouchers';

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
        | Informasi Voucher
        |--------------------------------------------------------------------------
        */

        'name',
        'code',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Tipe Voucher
        |--------------------------------------------------------------------------
        */

        'type',

        /*
        |--------------------------------------------------------------------------
        | Nilai Voucher
        |--------------------------------------------------------------------------
        */

        'value',
        'maximum_discount',
        'minimum_purchase',

        /*
        |--------------------------------------------------------------------------
        | Limit Voucher
        |--------------------------------------------------------------------------
        */

        'quota',
        'used_count',
        'max_usage_per_user',

        /*
        |--------------------------------------------------------------------------
        | Status Voucher
        |--------------------------------------------------------------------------
        */

        'status',
        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Jadwal Voucher
        |--------------------------------------------------------------------------
        */

        'start_at',
        'end_at',

        /*
        |--------------------------------------------------------------------------
        | Pengaturan Voucher
        |--------------------------------------------------------------------------
        */

        'is_stackable',
        'members_only',

        /*
        |--------------------------------------------------------------------------
        | Tampilan Voucher
        |--------------------------------------------------------------------------
        */

        'label',
        'badge_color',

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        'total_views',
        'total_claims',

        /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

        'meta_title',
        'meta_description',
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
            | Harga
            |--------------------------------------------------------------------------
            */

            'value' => 'integer',

            'maximum_discount' => 'integer',

            'minimum_purchase' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Integer
            |--------------------------------------------------------------------------
            */

            'quota' => 'integer',

            'used_count' => 'integer',

            'max_usage_per_user' => 'integer',

            'total_views' => 'integer',

            'total_claims' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            'is_active' => 'boolean',

            'is_stackable' => 'boolean',

            'members_only' => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'start_at' => 'datetime',

            'end_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi penggunaan voucher
     */
    public function usages()
    {
        return $this->hasMany(
            VoucherUsage::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek voucher aktif
     */
    public function getIsRunningAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->status !== 'active') {
            return false;
        }

        if (
            $this->start_at &&
            now()->lt($this->start_at)
        ) {
            return false;
        }

        if (
            $this->end_at &&
            now()->gt($this->end_at)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Mengecek voucher expired
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->end_at) {
            return false;
        }

        return now()->gt(
            $this->end_at
        );
    }

    /**
     * Mengecek quota voucher habis
     */
    public function getIsSoldOutAttribute(): bool
    {
        if (!$this->quota) {
            return false;
        }

        return
            $this->used_count >=
            $this->quota;
    }

    /**
     * Sisa quota voucher
     */
    public function getRemainingQuotaAttribute(): ?int
    {
        if (!$this->quota) {
            return null;
        }

        return max(
            0,
            $this->quota -
                $this->used_count
        );
    }

    /**
     * Label tipe voucher
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {

            'fixed' => 'Potongan Harga',

            'percent' => 'Diskon Persen',

            'free_shipping' => 'Gratis Ongkir',

            default => 'Voucher',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    /**
     * Voucher aktif
     */
    public function scopeActive($query)
    {
        return $query

            ->where(
                'is_active',
                true
            )

            ->where(
                'status',
                'active'
            );
    }

    /**
     * Voucher valid
     */
    public function scopeValid($query)
    {
        return $query

            ->active()

            ->where(function ($query) {

                $query

                    ->whereNull('start_at')

                    ->orWhere(
                        'start_at',
                        '<=',
                        now()
                    );
            })

            ->where(function ($query) {

                $query

                    ->whereNull('end_at')

                    ->orWhere(
                        'end_at',
                        '>=',
                        now()
                    );
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Increment penggunaan voucher
     */
    public function incrementUsage(
        int $count = 1
    ): void {
        $this->increment(
            'used_count',
            $count
        );

        $this->increment(
            'total_claims',
            $count
        );
    }

    /**
     * Increment views voucher
     */
    public function incrementViews(): void
    {
        $this->increment(
            'total_views'
        );
    }

    /**
     * Mengecek minimum pembelian
     */
    public function validateMinimumPurchase(
        int $subtotal
    ): bool {
        return
            $subtotal >=
            $this->minimum_purchase;
    }

    /**
     * Mengecek quota voucher
     */
    public function hasQuota(): bool
    {
        if (!$this->quota) {
            return true;
        }

        return
            $this->used_count <
            $this->quota;
    }

    /**
     * Menghitung jumlah discount
     */
    public function calculateDiscount(
        int $subtotal,
        int $shippingCost = 0
    ): int {

        /*
        |--------------------------------------------------------------------------
        | Free Shipping
        |--------------------------------------------------------------------------
        */

        if ($this->type === 'free_shipping') {

            return $shippingCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Fixed Discount
        |--------------------------------------------------------------------------
        */

        if ($this->type === 'fixed') {

            return min(
                $this->value,
                $subtotal
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Percent Discount
        |--------------------------------------------------------------------------
        */

        $discount = (int) floor(
            ($subtotal * $this->value) / 100
        );

        // Batasi max discount
        if ($this->maximum_discount) {

            $discount = min(
                $discount,
                $this->maximum_discount
            );
        }

        return min(
            $discount,
            $subtotal
        );
    }

    /**
     * Refresh status voucher otomatis
     */
    public function refreshStatus(): void
    {
        if (!$this->is_active) {

            $this->update([
                'status' => 'disabled',
            ]);

            return;
        }

        if (
            $this->end_at &&
            now()->gt($this->end_at)
        ) {

            $this->update([
                'status' => 'expired',
            ]);

            return;
        }

        $this->update([
            'status' => 'active',
        ]);
    }
}
