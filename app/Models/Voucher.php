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
        | Media
        |--------------------------------------------------------------------------
        */

        'image_path',

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

        // Rule flags
        'allow_on_flash_sale',
        'allow_on_discount',
        'exclude_digital',

        // Combination rules
        'is_combinable',
        'combination_type',

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

            // Rule flags
            'allow_on_flash_sale' => 'boolean',
            'allow_on_discount' => 'boolean',
            'exclude_digital' => 'boolean',

            // Combination
            'is_combinable' => 'boolean',
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
    | Relationships with products / categories / brands / shipping / payment
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'voucher_products',
            'voucher_id',
            'product_id'
        )->withPivot('is_excluded');
    }

    public function categories()
    {
        return $this->belongsToMany(
            \App\Models\Category::class,
            'voucher_categories',
            'voucher_id',
            'category_id'
        )->withPivot('is_excluded');
    }

    public function shippingMethods()
    {
        return $this->belongsToMany(
            \App\Models\ShippingMethod::class,
            'voucher_shipping_methods',
            'voucher_id',
            'shipping_method_id'
        )->withPivot('is_excluded');
    }

    public function paymentMethods()
    {
        return $this->belongsToMany(
            \App\Models\PaymentMethod::class,
            'voucher_payment_methods',
            'voucher_id',
            'payment_method_id'
        )->withPivot('is_excluded');
    }

    /**
     * Kombinasi voucher yang diizinkan dengan voucher ini
     */
    public function allowedCombinations()
    {
        return $this->hasMany(
            VoucherCombination::class,
            'voucher_a_id',
            'id'
        )->where('is_allowed', true);
    }

    /**
     * Kombinasi voucher reverse
     */
    public function reverseCombinations()
    {
        return $this->hasMany(
            VoucherCombination::class,
            'voucher_b_id',
            'id'
        )->where('is_allowed', true);
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

    /**
     * Validasi kombinasi dengan voucher lain
     */
    public function canCombineWith(Voucher $other): bool
    {
        // Both vouchers harus combinable
        if (!$this->is_combinable || !$other->is_combinable) {
            return false;
        }

        // Type check: tidak bisa combine diskon dengan potongan
        if ($this->getCombinationType() === $other->getCombinationType()) {
            // Same type tidak bisa dikombinasi
            $thisType = $this->type;
            $otherType = $other->type;

            if ($thisType === 'free_shipping' && $otherType === 'free_shipping') {
                return false; // Dua free shipping tidak boleh
            }

            if ($thisType !== 'free_shipping' && $otherType !== 'free_shipping') {
                return false; // Dua diskon/potongan tidak boleh
            }
        }

        // Check explicit rules
        return $this->isAllowedCombination($other);
    }

    /**
     * Get combination type (shipping vs discount)
     */
    public function getCombinationType(): string
    {
        if ($this->type === 'free_shipping') {
            return 'shipping';
        }

        return 'discount'; // fixed atau percent
    }

    /**
     * Check explicit allowed combination
     */
    private function isAllowedCombination(Voucher $other): bool
    {
        // Check both directions
        $exists = VoucherCombination::where(function ($q) use ($other) {
            $q->where('voucher_a_id', $this->id)
              ->where('voucher_b_id', $other->id);
        })->orWhere(function ($q) use ($other) {
            $q->where('voucher_a_id', $other->id)
              ->where('voucher_b_id', $this->id);
        })->where('is_allowed', true)->exists();

        return $exists;
    }
}
