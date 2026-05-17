<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Validation\ValidationException;

class DiscountService
{
    /*
    |--------------------------------------------------------------------------
    | Apply Voucher
    |--------------------------------------------------------------------------
    */

    /**
     * Apply voucher ke cart
     */
    public function applyVoucher(
        Cart $cart,
        string $voucherCode
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Cari voucher
        |--------------------------------------------------------------------------
        */

        $voucher = Voucher::query()

            ->valid()

            ->where(
                'code',
                strtoupper($voucherCode)
            )

            ->first();

        if (!$voucher) {

            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak valid.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi quota voucher
        |--------------------------------------------------------------------------
        */

        if (!$voucher->hasQuota()) {

            throw ValidationException::withMessages([
                'voucher' => 'Quota voucher sudah habis.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi minimum belanja
        |--------------------------------------------------------------------------
        */

        if (
            !$voucher->validateMinimumPurchase(
                $cart->subtotal
            )
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                'Minimum belanja untuk voucher ini adalah Rp' .
                    number_format(
                        $voucher->minimum_purchase,
                        0,
                        ',',
                        '.'
                    ),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi penggunaan voucher user
        |--------------------------------------------------------------------------
        */

        if (
            $cart->user &&
            $cart->user->voucherUsageCount(
                $voucher->id
            ) >= $voucher->max_usage_per_user
        ) {

            throw ValidationException::withMessages([
                'voucher' =>
                'Voucher sudah mencapai batas penggunaan.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Hitung discount
        |--------------------------------------------------------------------------
        */

        $discountAmount = 0;

        $shippingDiscount = 0;

        /*
        |--------------------------------------------------------------------------
        | Free Shipping Voucher
        |--------------------------------------------------------------------------
        */

        if ($voucher->type === 'free_shipping') {

            // Nanti shipping cost realtime
            // diambil saat checkout

            $shippingDiscount = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Product Discount Voucher
        |--------------------------------------------------------------------------
        */ else {

            $discountAmount =
                $voucher->calculateDiscount(
                    $cart->subtotal
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan voucher ke cart
        |--------------------------------------------------------------------------
        */

        $cart->update([

            'voucher_code' => $voucher->code,

            'voucher_name' => $voucher->name,

            'discount_amount' => $discountAmount,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Refresh summary cart
        |--------------------------------------------------------------------------
        */

        $cart->refreshCartSummary();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return [

            'success' => true,

            'message' => 'Voucher berhasil digunakan.',

            'voucher' => $voucher,

            'discount_amount' => $discountAmount,

            'shipping_discount' => $shippingDiscount,

            'final_subtotal' => $cart->fresh()->final_subtotal,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Voucher
    |--------------------------------------------------------------------------
    */

    /**
     * Hapus voucher cart
     */
    public function removeVoucher(
        Cart $cart
    ): array {

        $cart->clearVoucher();

        return [

            'success' => true,

            'message' => 'Voucher berhasil dihapus.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Voucher
    |--------------------------------------------------------------------------
    */

    /**
     * Validasi voucher sebelum checkout
     */
    public function validateVoucher(
        Cart $cart
    ): bool {

        if (!$cart->voucher_code) {
            return true;
        }

        $voucher = Voucher::query()

            ->valid()

            ->where(
                'code',
                $cart->voucher_code
            )

            ->first();

        if (!$voucher) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi minimum belanja
        |--------------------------------------------------------------------------
        */

        if (
            !$voucher->validateMinimumPurchase(
                $cart->subtotal
            )
        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi quota
        |--------------------------------------------------------------------------
        */

        if (!$voucher->hasQuota()) {

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Voucher Usage
    |--------------------------------------------------------------------------
    */

    /**
     * Simpan histori penggunaan voucher
     */
    public function createVoucherUsage(
        Voucher $voucher,
        array $data
    ): VoucherUsage {

        /*
        |--------------------------------------------------------------------------
        | Increment voucher usage
        |--------------------------------------------------------------------------
        */

        $voucher->incrementUsage();

        /*
        |--------------------------------------------------------------------------
        | Simpan histori penggunaan
        |--------------------------------------------------------------------------
        */

        return VoucherUsage::create([

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            'voucher_id' => $voucher->id,

            'user_id' => $data['user_id'] ?? null,

            'order_id' => $data['order_id'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Snapshot Voucher
            |--------------------------------------------------------------------------
            */

            'voucher_name' => $voucher->name,

            'voucher_code' => $voucher->code,

            'voucher_type' => $voucher->type,

            /*
            |--------------------------------------------------------------------------
            | Snapshot Discount
            |--------------------------------------------------------------------------
            */

            'voucher_value' => $voucher->value,

            'discount_amount' =>
            $data['discount_amount'] ?? 0,

            'shipping_discount' =>
            $data['shipping_discount'] ?? 0,

            /*
            |--------------------------------------------------------------------------
            | Snapshot Order
            |--------------------------------------------------------------------------
            */

            'invoice_number' =>
            $data['invoice_number'] ?? null,

            'order_subtotal' =>
            $data['order_subtotal'] ?? 0,

            'order_grand_total' =>
            $data['order_grand_total'] ?? 0,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => 'used',

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'used_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Flash Sale
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek produk sedang flash sale
     */
    public function isFlashSaleProduct(
        $product
    ): bool {

        return $product->is_flash_sale;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Format rupiah
     */
    protected function rupiah(
        int $amount
    ): string {

        return 'Rp' .
            number_format(
                $amount,
                0,
                ',',
                '.'
            );
    }
}
