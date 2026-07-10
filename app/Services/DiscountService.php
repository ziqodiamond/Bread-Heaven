<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Validation\ValidationException;
use App\Services\VoucherService;

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

        // Delegasikan validasi ke VoucherService agar semua rule terpusat
        $voucherService = new VoucherService();

        $result = $voucherService->validate($cart, $voucherCode);

        $voucher = $result['voucher'];

        $discountAmount = $result['discount_amount'] ?? 0;
        $shippingDiscount = $result['shipping_discount'] ?? 0;

        // Siapkan snapshot voucher (agar frontend menampilkan dan untuk order snapshot nanti)
        $snapshot = [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'type' => $voucher->type,
            'value' => $voucher->value,
            'maximum_discount' => $voucher->maximum_discount,
        ];

        // Simpan snapshot voucher ke cart
        $cart->update([
            'voucher_code' => $voucher->code,
            'voucher_name' => $voucher->name,
            'discount_amount' => $discountAmount,
            'voucher_snapshot' => $snapshot,
        ]);

        $cart->refreshCartSummary();

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
