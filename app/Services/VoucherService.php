<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service terpusat untuk validasi dan perhitungan voucher
 * Semua logic validasi berada di sini agar tidak tersebar di Controller/Model
 */
class VoucherService
{
    /**
     * Validasi voucher untuk cart.
     * Mengembalikan array berisi: success, message, discount_amount, shipping_discount, voucher
     */
    public function validate(Cart $cart, string $voucherCode): array
    {
        // Cari voucher yang valid (aktif & jadwal)
        $voucher = Voucher::query()
            ->valid()
            ->where('code', strtoupper($voucherCode))
            ->first();

        if (!$voucher) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak valid.']);
        }

        // Urutan validasi sesuai requirement
        // 1. Aktif
        if (!$voucher->is_running) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak aktif atau berada diluar jadwal.']);
        }

        // 2. Belum expired
        if ($voucher->is_expired) {
            throw ValidationException::withMessages(['voucher' => 'Voucher sudah kadaluarsa.']);
        }

        // 3. Kuota tersedia
        if (!$this->validateQuota($voucher)) {
            throw ValidationException::withMessages(['voucher' => 'Kuota voucher sudah habis.']);
        }

        // 4. User memenuhi syarat
        if (!$this->validateUser($voucher, $cart->user)) {
            throw ValidationException::withMessages(['voucher' => 'Anda tidak memenuhi syarat untuk voucher ini.']);
        }

        // 5. Limit per user
        if (!$this->validateUserUsageLimit($voucher, $cart->user)) {
            throw ValidationException::withMessages(['voucher' => 'Anda sudah mencapai batas penggunaan voucher ini.']);
        }

        // 6. Minimum pembelian
        if (!$this->validateMinimumPurchase($voucher, $cart->subtotal)) {
            throw ValidationException::withMessages(['voucher' => 'Minimum pembelian belum tercapai.']);
        }

        // 7-9. Produk / Kategori / Brand
        if (!$this->validateProducts($voucher, $cart->items)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak berlaku untuk produk/varian yang ada di keranjang.']);
        }

        if (!$this->validateCategories($voucher, $cart->items)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak berlaku untuk kategori produk di keranjang.']);
        }

        if (!$this->validateBrands($voucher, $cart->items)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak berlaku untuk brand produk di keranjang.']);
        }

        // 10. Shipping
        if (!$this->validateShipping($voucher, $cart->selected_shipping_method ?? null)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak berlaku untuk metode pengiriman ini.']);
        }

        // 11. Payment
        if (!$this->validatePayment($voucher, $cart->selected_payment_method ?? null)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak berlaku untuk metode pembayaran ini.']);
        }

        // 12. Flash Sale
        if (!$this->validateFlashSale($voucher, $cart->items)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak dapat digunakan bersamaan dengan Flash Sale.']);
        }

        // 13. Discount (produk diskon)
        if (!$this->validateDiscount($voucher, $cart->items)) {
            throw ValidationException::withMessages(['voucher' => 'Voucher tidak dapat digunakan untuk produk yang sedang diskon.']);
        }

        // Hitung diskon
        $shippingCost = $cart->shipping_cost ?? 0;
        $discountAmount = $this->calculateDiscount($voucher, $cart->subtotal, $shippingCost);

        return [
            'success' => true,
            'message' => 'Voucher valid.',
            'voucher' => $voucher,
            'discount_amount' => $discountAmount,
            'shipping_discount' => $voucher->type === 'free_shipping' ? min($shippingCost, $discountAmount) : 0,
        ];
    }

    /**
     * Cek kuota voucher
     */
    public function validateQuota(Voucher $voucher): bool
    {
        return $voucher->hasQuota();
    }

    /**
     * Validasi user (all/new/member)
     * Untuk pengembangan, gunakan kolom members_only dan additional rules
     */
    public function validateUser(Voucher $voucher, $user = null): bool
    {
        if ($voucher->members_only) {
            return $user !== null;
        }

        // Default: semua user boleh
        return true;
    }

    /**
     * Validasi batas pemakaian per user
     */
    public function validateUserUsageLimit(Voucher $voucher, $user = null): bool
    {
        if (!$user) {
            return true;
        }

        $count = VoucherUsage::query()->where('voucher_id', $voucher->id)->where('user_id', $user->id)->count();

        return $count < $voucher->max_usage_per_user;
    }

    /**
     * Validasi minimum pembelian
     */
    public function validateMinimumPurchase(Voucher $voucher, int $subtotal): bool
    {
        return $subtotal >= $voucher->minimum_purchase;
    }

    /**
     * Validasi produk — tangent inclusion/exclusion
     */
    public function validateProducts(Voucher $voucher, $cartItems): bool
    {
        // Jika voucher tidak meng-include product list (artinya berlaku untuk semua), check excludes only
        $included = $voucher->products()->wherePivot('is_excluded', false)->pluck('id')->toArray();
        $excluded = $voucher->products()->wherePivot('is_excluded', true)->pluck('id')->toArray();

        foreach ($cartItems as $item) {
            $productId = $item->product_id ?? ($item->product->id ?? null);
            if (!$productId) continue;

            // Jika ada included list dan product tidak ada di included -> invalid
            if (!empty($included) && !in_array($productId, $included, true)) {
                return false;
            }

            // Jika product ada di excluded -> invalid
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
        $included = $voucher->categories()->wherePivot('is_excluded', false)->pluck('id')->toArray();
        $excluded = $voucher->categories()->wherePivot('is_excluded', true)->pluck('id')->toArray();

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
     * Validasi brand
     */
    public function validateBrands(Voucher $voucher, $cartItems): bool
    {
        $included = $voucher->brands()->wherePivot('is_excluded', false)->pluck('id')->toArray();
        $excluded = $voucher->brands()->wherePivot('is_excluded', true)->pluck('id')->toArray();

        if (empty($included) && empty($excluded)) {
            return true;
        }

        foreach ($cartItems as $item) {
            $product = $item->product ?? null;
            if (!$product) continue;

            $brandId = $product->brand_id ?? null;
            if (!$brandId) continue;

            if (!empty($included) && !in_array($brandId, $included, true)) {
                return false;
            }

            if (!empty($excluded) && in_array($brandId, $excluded, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validasi metode pengiriman
     */
    public function validateShipping(Voucher $voucher, $shippingMethod = null): bool
    {
        if (!$shippingMethod) {
            return true;
        }

        $included = $voucher->shippingMethods()->wherePivot('is_excluded', false)->pluck('id')->toArray();
        $excluded = $voucher->shippingMethods()->wherePivot('is_excluded', true)->pluck('id')->toArray();

        if (!empty($included) && !in_array($shippingMethod->id, $included, true)) {
            return false;
        }

        if (!empty($excluded) && in_array($shippingMethod->id, $excluded, true)) {
            return false;
        }

        return true;
    }

    /**
     * Validasi metode pembayaran
     */
    public function validatePayment(Voucher $voucher, $paymentMethod = null): bool
    {
        if (!$paymentMethod) {
            return true;
        }

        $included = $voucher->paymentMethods()->wherePivot('is_excluded', false)->pluck('id')->toArray();
        $excluded = $voucher->paymentMethods()->wherePivot('is_excluded', true)->pluck('id')->toArray();

        if (!empty($included) && !in_array($paymentMethod->id, $included, true)) {
            return false;
        }

        if (!empty($excluded) && in_array($paymentMethod->id, $excluded, true)) {
            return false;
        }

        return true;
    }

    /**
     * Validasi rule flash sale
     */
    public function validateFlashSale(Voucher $voucher, $cartItems): bool
    {
        // Jika voucher mengizinkan penggunaan pada flash sale, ok
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
     * Validasi rule produk diskon
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
     * Hitung nominal diskon menggunakan logika Voucher::calculateDiscount
     */
    public function calculateDiscount(Voucher $voucher, int $subtotal, int $shippingCost = 0): int
    {
        return $voucher->calculateDiscount($subtotal, $shippingCost);
    }

    /**
     * Create voucher usage snapshot and increment usage within transaction
     */
    public function applyVoucherToOrder(Voucher $voucher, array $data): VoucherUsage
    {
        return DB::transaction(function () use ($voucher, $data) {
            // increment usage
            $voucher->incrementUsage(1);

            // create usage snapshot
            return VoucherUsage::create([
                'voucher_id' => $voucher->id,
                'user_id' => $data['user_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'voucher_name' => $voucher->name,
                'voucher_code' => $voucher->code,
                'voucher_type' => $voucher->type,
                'voucher_value' => $voucher->value,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'shipping_discount' => $data['shipping_discount'] ?? 0,
                'invoice_number' => $data['invoice_number'] ?? null,
                'order_subtotal' => $data['order_subtotal'] ?? 0,
                'order_grand_total' => $data['order_grand_total'] ?? 0,
                'status' => 'used',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'used_at' => now(),
            ]);
        });
    }

    /**
     * Cek apakah voucher dapat dipakai sekarang (public helper)
     */
    public function canUse(Voucher $voucher, Cart $cart): bool
    {
        try {
            $this->validate($cart, $voucher->code);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
