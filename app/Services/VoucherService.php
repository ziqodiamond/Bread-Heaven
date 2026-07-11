<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\VoucherCombination;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Enhanced Voucher Service dengan dukungan kombinasi voucher
 * 
 * Rules:
 * - Max 2 voucher per cart
 * - Harus berbeda type (shipping vs discount)
 * - Tidak bisa 2x diskon atau 2x free_shipping
 * - Voucher A dan B harus both is_combinable = true
 * - Cek explicit allowed combinations di voucher_combinations table
 */
class VoucherService
{
    /**
     * Validasi dan tambah voucher ke cart
     */
    public function addVoucher(Cart $cart, string $voucherCode): array
    {
        try {
            $voucher = $this->findAndValidateVoucher($voucherCode);

            // Cek apakah voucher sudah diaplikasikan
            $currentVouchers = $cart->vouchers ?? [];
            if ($this->voucherAlreadyApplied($voucher->id, $currentVouchers)) {
                throw ValidationException::withMessages([
                    'voucher' => 'Voucher ini sudah diaplikasikan pada keranjang.'
                ]);
            }

            // Cek batas jumlah voucher (max 2)
            if (count($currentVouchers) >= 2) {
                throw ValidationException::withMessages([
                    'voucher' => 'Maksimal 2 voucher dapat digunakan. Silakan hapus voucher lain terlebih dahulu.'
                ]);
            }

            // Jika sudah ada 1 voucher, cek kombinasi
            if (count($currentVouchers) > 0) {
                $existingVoucher = Voucher::find($currentVouchers[0]['id']);
                $this->validateCombination($existingVoucher, $voucher);
            }

            // Validasi kuota dan rules
            $this->validateQuotaAndRules($voucher, $cart);

            // Hitung discount
            $discountData = $this->calculateVoucherDiscount($voucher, $cart);

            // Tambahkan ke array vouchers
            $newVouchers = $currentVouchers;
            $newVouchers[] = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'discount_amount' => $discountData['discount_amount'],
                'shipping_discount' => $discountData['shipping_discount'],
                'value' => $voucher->value,
            ];

            // Update cart dengan semua vouchers
            $this->updateCartVouchers($cart, $newVouchers);

            return [
                'success' => true,
                'message' => "Voucher '{$voucher->name}' berhasil diterapkan.",
                'voucher' => $voucher,
                'discount_data' => $discountData,
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'voucher' => $e->getMessage() ?: 'Terjadi kesalahan saat menerapkan voucher.'
            ]);
        }
    }

    /**
     * Hapus voucher dari cart
     */
    public function removeVoucher(Cart $cart, string $voucherId): array
    {
        $currentVouchers = $cart->vouchers ?? [];
        $newVouchers = array_filter(
            $currentVouchers,
            fn($v) => $v['id'] !== $voucherId
        );

        $this->updateCartVouchers($cart, array_values($newVouchers));

        return [
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
        ];
    }

    /**
     * Cari dan validasi voucher
     */
    private function findAndValidateVoucher(string $voucherCode): Voucher
    {
        $voucher = Voucher::query()
            ->valid()
            ->where('code', strtoupper($voucherCode))
            ->first();

        if (!$voucher) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak valid atau tidak tersedia.'
            ]);
        }

        if (!$voucher->is_running) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak aktif atau berada di luar jadwal.'
            ]);
        }

        if ($voucher->is_expired) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher sudah kadaluarsa.'
            ]);
        }

        return $voucher;
    }

    /**
     * Cek apakah voucher sudah diaplikasikan
     */
    private function voucherAlreadyApplied(string $voucherId, array $currentVouchers): bool
    {
        return !empty(array_filter(
            $currentVouchers,
            fn($v) => $v['id'] === $voucherId
        ));
    }

    /**
     * Validasi kombinasi dua voucher
     * 
     * Rules:
     * - Both harus is_combinable = true
     * - Harus berbeda type (shipping vs discount)
     * - Cek explicit allowed combinations
     */
    private function validateCombination(Voucher $existing, Voucher $new): void
    {
        // Cek is_combinable pada kedua voucher
        if (!$existing->is_combinable || !$new->is_combinable) {
            throw ValidationException::withMessages([
                'voucher' => 'Salah satu atau kedua voucher ini tidak dapat dikombinasikan dengan voucher lain.'
            ]);
        }

        // Cek type - tidak boleh sama
        $existingType = $existing->getCombinationType();
        $newType = $new->getCombinationType();

        if ($existingType === $newType) {
            throw ValidationException::withMessages([
                'voucher' => 'Tidak dapat menggabungkan dua voucher dengan tipe sama. Pilih satu voucher untuk diskon dan satu untuk gratis ongkir.'
            ]);
        }

        // Cek explicit allowed combinations
        if (!$this->isExplicitlyAllowed($existing, $new)) {
            throw ValidationException::withMessages([
                'voucher' => "Voucher '{$new->name}' tidak dapat dikombinasikan dengan '{$existing->name}'."
            ]);
        }
    }

    /**
     * Cek explicit allowed combination di database
     */
    private function isExplicitlyAllowed(Voucher $v1, Voucher $v2): bool
    {
        // Cek kedua arah
        $exists = VoucherCombination::where(function ($q) use ($v1, $v2) {
            $q->where('voucher_a_id', $v1->id)
              ->where('voucher_b_id', $v2->id);
        })->orWhere(function ($q) use ($v1, $v2) {
            $q->where('voucher_a_id', $v2->id)
              ->where('voucher_b_id', $v1->id);
        })->where('is_allowed', true)->exists();

        return $exists;
    }

    /**
     * Validasi kuota dan rules voucher
     */
    private function validateQuotaAndRules(Voucher $voucher, Cart $cart): void
    {
        // 1. Kuota
        if (!$this->validateQuota($voucher)) {
            throw ValidationException::withMessages([
                'voucher' => 'Kuota voucher sudah habis. Gunakan voucher lain.'
            ]);
        }

        // 2. User
        if (!$this->validateUser($voucher, $cart->user)) {
            throw ValidationException::withMessages([
                'voucher' => 'Anda tidak memenuhi syarat untuk voucher ini.'
            ]);
        }

        // 3. Usage limit per user
        if (!$this->validateUserUsageLimit($voucher, $cart->user)) {
            throw ValidationException::withMessages([
                'voucher' => 'Anda sudah mencapai batas penggunaan voucher ini.'
            ]);
        }

        // 4. Minimum purchase
        if (!$this->validateMinimumPurchase($voucher, $cart->subtotal)) {
            $formatted = 'Rp' . number_format($voucher->minimum_purchase, 0, ',', '.');
            throw ValidationException::withMessages([
                'voucher' => "Minimum pembelian belum tercapai. Minimum: {$formatted}"
            ]);
        }

        // 5. Products
        if (!$this->validateProducts($voucher, $cart->items)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak berlaku untuk produk di keranjang.'
            ]);
        }

        // 6. Categories
        if (!$this->validateCategories($voucher, $cart->items)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak berlaku untuk kategori produk di keranjang.'
            ]);
        }

        // 7. Shipping methods (jika ada)
        if ($cart->selected_shipping_method && !$this->validateShipping($voucher, $cart->selected_shipping_method)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak berlaku untuk metode pengiriman ini.'
            ]);
        }

        // 8. Payment methods (jika ada)
        if ($cart->selected_payment_method && !$this->validatePayment($voucher, $cart->selected_payment_method)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak berlaku untuk metode pembayaran ini.'
            ]);
        }

        // 9. Flash sale
        if (!$this->validateFlashSale($voucher, $cart->items)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak dapat digunakan untuk produk flash sale.'
            ]);
        }

        // 10. Discount products
        if (!$this->validateDiscount($voucher, $cart->items)) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak dapat digunakan untuk produk yang sedang diskon.'
            ]);
        }
    }

    /**
     * Hitung discount dari satu voucher
     */
    private function calculateVoucherDiscount(Voucher $voucher, Cart $cart): array
    {
        $shippingCost = $cart->shipping_cost ?? 0;
        $discountAmount = $voucher->calculateDiscount($cart->subtotal, $shippingCost);
        $shippingDiscount = $voucher->type === 'free_shipping' ? min($shippingCost, $discountAmount) : 0;

        return [
            'discount_amount' => $discountAmount,
            'shipping_discount' => $shippingDiscount,
        ];
    }

    /**
     * Update cart dengan semua vouchers dan recalculate total
     */
    private function updateCartVouchers(Cart $cart, array $newVouchers): void
    {
        // Calculate totals
        $totalDiscountAmount = 0;
        $totalShippingDiscount = 0;

        foreach ($newVouchers as $vData) {
            $totalDiscountAmount += $vData['discount_amount'] ?? 0;
            $totalShippingDiscount += $vData['shipping_discount'] ?? 0;
        }

        // Update cart
        $cart->update([
            'vouchers' => $newVouchers,
            'total_discount_amount' => $totalDiscountAmount,
            'total_shipping_discount' => $totalShippingDiscount,
            'discount_amount' => $totalDiscountAmount,
        ]);

        // Refresh summary
        $cart->refreshCartSummary();
    }

    /**
     * Validasi kuota voucher
     */
    public function validateQuota(Voucher $voucher): bool
    {
        return $voucher->hasQuota();
    }

    /**
     * Validasi user (members only, etc)
     */
    public function validateUser(Voucher $voucher, $user = null): bool
    {
        if ($voucher->members_only) {
            return $user !== null;
        }

        return true;
    }

    /**
     * Validasi batas penggunaan per user
     */
    public function validateUserUsageLimit(Voucher $voucher, $user = null): bool
    {
        if (!$user) {
            return true;
        }

        $count = VoucherUsage::query()
            ->where('voucher_id', $voucher->id)
            ->where('user_id', $user->id)
            ->where('status', 'used')
            ->count();

        return $count < ($voucher->max_usage_per_user ?? 1);
    }

    /**
     * Validasi minimum pembelian
     */
    public function validateMinimumPurchase(Voucher $voucher, int $subtotal): bool
    {
        return $subtotal >= ($voucher->minimum_purchase ?? 0);
    }

    /**
     * Validasi produk
     */
    public function validateProducts(Voucher $voucher, $cartItems): bool
    {
        $included = $voucher->products()->wherePivot('is_excluded', false)->distinct()->select('products.id')->get()->pluck('id')->toArray();
        $excluded = $voucher->products()->wherePivot('is_excluded', true)->distinct()->select('products.id')->get()->pluck('id')->toArray();

        foreach ($cartItems as $item) {
            $productId = $item->product_id ?? ($item->product->id ?? null);
            if (!$productId) continue;

            if (!empty($included) && !in_array($productId, $included, true)) {
                return false;
            }

            if (!empty($excluded) && in_array($productId, $excluded, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validasi kategori
     */
    public function validateCategories(Voucher $voucher, $cartItems): bool
    {
        $included = $voucher->categories()->wherePivot('is_excluded', false)->distinct()->select('categories.id')->get()->pluck('id')->toArray();
        $excluded = $voucher->categories()->wherePivot('is_excluded', true)->distinct()->select('categories.id')->get()->pluck('id')->toArray();

        if (empty($included) && empty($excluded)) {
            return true;
        }

        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) continue;

            $catId = $product->category_id ?? null;
            if (!$catId) continue;

            if (!empty($included) && !in_array($catId, $included, true)) {
                return false;
            }

            if (!empty($excluded) && in_array($catId, $excluded, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validasi shipping method
     */
    public function validateShipping(Voucher $voucher, $shippingMethod = null): bool
    {
        if (!$shippingMethod) {
            return true;
        }

        $included = $voucher->shippingMethods()->wherePivot('is_excluded', false)->distinct()->select('shipping_methods.id')->get()->pluck('id')->toArray();
        $excluded = $voucher->shippingMethods()->wherePivot('is_excluded', true)->distinct()->select('shipping_methods.id')->get()->pluck('id')->toArray();

        if (!empty($included) && !in_array($shippingMethod->id, $included, true)) {
            return false;
        }

        if (!empty($excluded) && in_array($shippingMethod->id, $excluded, true)) {
            return false;
        }

        return true;
    }

    /**
     * Validasi payment method
     */
    public function validatePayment(Voucher $voucher, $paymentMethod = null): bool
    {
        if (!$paymentMethod) {
            return true;
        }

        $included = $voucher->paymentMethods()->wherePivot('is_excluded', false)->distinct()->select('payment_methods.id')->get()->pluck('id')->toArray();
        $excluded = $voucher->paymentMethods()->wherePivot('is_excluded', true)->distinct()->select('payment_methods.id')->get()->pluck('id')->toArray();

        if (!empty($included) && !in_array($paymentMethod->id, $included, true)) {
            return false;
        }

        if (!empty($excluded) && in_array($paymentMethod->id, $excluded, true)) {
            return false;
        }

        return true;
    }

    /**
     * Validasi flash sale
     */
    public function validateFlashSale(Voucher $voucher, $cartItems): bool
    {
        if ($voucher->allow_on_flash_sale) {
            return true;
        }

        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) continue;

            if ($product->is_flash_sale) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validasi discount
     */
    public function validateDiscount(Voucher $voucher, $cartItems): bool
    {
        if ($voucher->allow_on_discount) {
            return true;
        }

        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) continue;

            if ($product->has_active_discount) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply multiple vouchers to order (dengan tracking penggunaan)
     * Support 2 signatures:
     * 1. Old: applyVouchersToOrder(array of IDs, array of order data) -> array
     * 2. New: applyVouchersToOrder(array with 'voucher', 'order' keys) -> mixed
     */
    public function applyVouchersToOrder(array $voucherIds, array $orderData = []): mixed
    {
        // Check if this is new signature (config array with 'voucher' key)
        if (isset($voucherIds['voucher']) && isset($voucherIds['order'])) {
            $this->applyVoucherToOrderConfig($voucherIds);
            return null;
        }

        // Old signature: array of IDs
        return DB::transaction(function () use ($voucherIds, $orderData) {
            $appliedVouchers = [];

            foreach ($voucherIds as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId);

                // Increment usage
                $voucher->incrementUsage(1);

                // Create usage snapshot
                $usage = VoucherUsage::create([
                    'voucher_id' => $voucher->id,
                    'user_id' => $orderData['user_id'] ?? null,
                    'order_id' => $orderData['order_id'] ?? null,
                    'voucher_name' => $voucher->name,
                    'voucher_code' => $voucher->code,
                    'voucher_type' => $voucher->type,
                    'voucher_value' => $voucher->value,
                    'discount_amount' => $orderData['discounts'][$voucherId]['discount_amount'] ?? 0,
                    'shipping_discount' => $orderData['discounts'][$voucherId]['shipping_discount'] ?? 0,
                    'invoice_number' => $orderData['invoice_number'] ?? null,
                    'order_subtotal' => $orderData['order_subtotal'] ?? 0,
                    'order_grand_total' => $orderData['order_grand_total'] ?? 0,
                    'status' => 'used',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'used_at' => now(),
                ]);

                $appliedVouchers[] = $usage;
            }

            return $appliedVouchers;
        });
    }

    /**
     * Apply single voucher to order (helper untuk CheckoutController)
     */
    private function applyVoucherToOrderConfig(array $config): void
    {
        DB::transaction(function () use ($config) {
            $voucher = $config['voucher'];
            $order = $config['order'];
            $user = $config['user'] ?? null;
            $discountAmount = $config['discount_amount'] ?? 0;
            $shippingDiscount = $config['shipping_discount'] ?? 0;

            // Increment usage
            $voucher->incrementUsage(1);

            // Create usage snapshot
            VoucherUsage::create([
                'voucher_id' => $voucher->id,
                'user_id' => $user?->id,
                'order_id' => $order->id,
                'voucher_name' => $voucher->name,
                'voucher_code' => $voucher->code,
                'voucher_type' => $voucher->type,
                'voucher_value' => $voucher->value,
                'discount_amount' => $discountAmount,
                'shipping_discount' => $shippingDiscount,
                'invoice_number' => $order->invoice_number,
                'order_subtotal' => $order->subtotal,
                'order_grand_total' => $order->grand_total,
                'status' => 'used',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'used_at' => now(),
            ]);
        });
    }

    /**
     * Get list available vouchers untuk display
     */
    public function getAvailableVouchers(int $limit = 10): Collection
    {
        return Voucher::valid()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'code' => $v->code,
                'name' => $v->name,
                'description' => $v->description,
                'type' => $v->type,
                'type_label' => $v->type_label,
                'value' => $v->value,
                'maximum_discount' => $v->maximum_discount,
                'label' => $v->label,
                'badge_color' => $v->badge_color ?? '#FF6B6B',
                'is_sold_out' => $v->is_sold_out,
                'remaining_quota' => $v->remaining_quota,
                'quota' => $v->quota,
                'used_count' => $v->used_count ?? 0,
                'is_combinable' => $v->is_combinable,
                'minimum_purchase' => $v->minimum_purchase,
                'image_url' => $v->image_url,
                'end_at' => $v->end_at?->toIso8601String(),
                'members_only' => $v->members_only ?? false,
                'max_usage_per_user' => $v->max_usage_per_user ?? 1,
            ]);
    }
}
