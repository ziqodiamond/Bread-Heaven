<x-layout>
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <!-- Heading -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Shopping Cart</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="item-count-label">
                    <span id="item-count">{{ $cart->items->count() }}</span> item di keranjang anda
                </p>
            </div>

            {{-- Alpine Cart Data --}}
            <div x-data="cartApp()" x-init="init()">
                @if ($cart->items->count() > 0)
                    <div class="lg:flex lg:items-start lg:gap-8">
                        <!-- Cart Items Section -->
                        <div class="w-full space-y-4 lg:w-2/3">
                            <template x-for="item in items" :key="item.id">
                                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                                        <!-- Product Image -->
                                        <div class="shrink-0">
                                            <img class="h-24 w-24 rounded-xl object-cover" :src="item.product_image_url" :alt="item.product_name">
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="item.product_name"></h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Rp<span x-text="formatPrice(item.product_price)"></span>
                                            </p>

                                            <!-- Action -->
                                            <div class="mt-4 flex flex-wrap items-center gap-4">
                                                <!-- Quantity Control -->
                                                <div class="flex items-center overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">
                                                    <button @click="updateQuantity(item.id, item.quantity - 1)" type="button"
                                                        class="flex h-10 w-10 items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition"
                                                        :disabled="item.quantity <= 1 || loading">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </button>

                                                    <input type="text" :value="item.quantity" readonly
                                                        class="h-10 w-14 border-0 bg-white text-center text-sm font-semibold text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white">

                                                    <button @click="updateQuantity(item.id, item.quantity + 1)" type="button"
                                                        class="flex h-10 w-10 items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition"
                                                        :disabled="loading">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Remove -->
                                                <button @click="removeItem(item.id)" type="button"
                                                    class="inline-flex items-center gap-2 text-sm font-medium text-red-600 hover:underline transition"
                                                    :disabled="loading">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="text-end">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white">
                                                Rp<span x-text="formatPrice(item.subtotal)"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Sidebar -->
                        <div class="mt-6 w-full lg:mt-0 lg:w-1/3">
                            <!-- Vouchers Section -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pilih Voucher</h3>

                                <!-- Applied Vouchers -->
                                <template x-if="appliedVouchers.length > 0 || voucherWarnings.length > 0">
                                    <div class="mb-4 space-y-2">
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Voucher Aktif</p>

                                        <!-- Warnings when vouchers were invalidated -->
                                        <template x-if="voucherWarnings.length > 0">
                                            <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700 dark:bg-red-900/20 dark:border-red-800">
                                                <template x-for="(w, idx) in voucherWarnings" :key="idx">
                                                    <div x-text="w"></div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-for="voucher in appliedVouchers" :key="voucher.id">
                                            <div class="flex items-start gap-3 p-3 rounded-lg border border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20 hover:bg-green-100 transition">
                                                <div class="shrink-0">
                                                    <div class="h-16 w-16 rounded-lg overflow-hidden bg-white dark:bg-gray-700 flex items-center justify-center">
                                                        <template x-if="voucher.image_url">
                                                            <img :src="voucher.image_url" :alt="voucher.name" class="h-full w-full object-cover">
                                                        </template>
                                                        <template x-if="!voucher.image_url">
                                                            <svg class="h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                            </svg>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-sm text-green-800 dark:text-green-300 truncate" x-text="voucher.name"></h4>
                                                    <p class="text-xs text-green-700 dark:text-green-400 mt-1" x-show="voucher.type === 'fixed'" x-text="`Rp${formatPrice(voucher.value)}`"></p>
                                                    <p class="text-xs text-green-700 dark:text-green-400 mt-1" x-show="voucher.type === 'percent'" x-text="`${voucher.value}% Diskon`"></p>
                                                    <p class="text-xs text-green-700 dark:text-green-400 mt-1" x-show="voucher.type === 'free_shipping'">Gratis Ongkir</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="voucher.description"></p>
                                                    <div class="mt-2 flex items-center gap-2">
                                                        <button @click="showVoucherDetail(voucher)" type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Selengkapnya →</button>
                                                        <button @click="removeVoucher(voucher.id)" type="button" class="text-xs text-red-600 hover:text-red-800 font-medium">Batal</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Available Vouchers -->
                                <div class="space-y-3 max-h-96 overflow-y-auto">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Voucher Tersedia</p>
                                    <template x-for="voucher in availableVouchers" :key="voucher.id">
                                        <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <!-- Left: Image -->
                                            <div class="shrink-0">
                                                <div class="h-16 w-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                    <template x-if="voucher.image_url">
                                                                                                            <img :src="voucher.image_url" :alt="voucher.name" class="h-full w-full object-cover">
                                                    </template>
                                                                                                        <template x-if="!voucher.image_url">
                                                        <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Middle: Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="voucher.name"></h4>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                    <span x-show="voucher.type === 'fixed'" x-text="`Rp${formatPrice(voucher.value)}`"></span>
                                                    <span x-show="voucher.type === 'percent'" x-text="`${voucher.value}% Diskon`"></span>
                                                    <span x-show="voucher.type === 'free_shipping'">Gratis Ongkir</span>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-show="voucher.minimum_purchase > 0">
                                                    Min. Rp<span x-text="formatPrice(voucher.minimum_purchase)"></span>
                                                </p>
                                                <button @click="showVoucherDetail(voucher)" type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium mt-1">
                                                    Selengkapnya →
                                                </button>
                                            </div>

                                            <!-- Right: Button -->
                                            <div class="shrink-0">
                                                <button @click="applyVoucher(voucher.id)" type="button"
                                                    :disabled="!voucher.can_apply || loading || (appliedVouchers.length >= 2 && !isVoucherApplied(voucher.id))"
                                                    :class="voucher.can_apply ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'"
                                                    class="px-3 py-2 rounded-lg font-medium text-xs transition whitespace-nowrap">
                                                    Pakai
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="availableVouchers.length === 0 && !loadingVouchers">
                                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">Tidak ada voucher tersedia</p>
                                    </template>

                                    <template x-if="loadingVouchers">
                                        <div class="flex justify-center py-6">
                                            <svg class="h-6 w-6 animate-spin text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h3>

                                <div class="mt-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Total Item</span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="summary.total_items"></span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            Rp<span x-text="formatPrice(summary.subtotal)"></span>
                                        </span>
                                    </div>

                                    <template x-if="summary.discount_amount > 0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                                            <span class="font-medium text-green-600 dark:text-green-400">
                                                -Rp<span x-text="formatPrice(summary.discount_amount)"></span>
                                            </span>
                                        </div>
                                    </template>

                                    <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                            <span class="text-xl font-bold text-gray-900 dark:text-white">
                                                Rp<span x-text="formatPrice(summary.final_subtotal)"></span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Checkout -->
                                    <a href="{{ route('checkout.index') }}"
                                        class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700">
                                        Checkout
                                    </a>

                                    <!-- Continue Shopping -->
                                    <a href="{{ route('products') }}"
                                        class="flex w-full items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                        Lanjut Belanja
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty Cart -->
                    <div class="flex flex-col items-center justify-center rounded-2xl bg-white py-20 text-center shadow-sm dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2 5h12m-8 0a1 1 0 102 0m-2 0a1 1 0 112 0" />
                        </svg>
                        <h3 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Cart masih kosong</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Yuk tambahin produk dulu ke cart lu.</p>
                        <a href="{{ route('products') }}" class="mt-6 rounded-xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-700">Belanja Sekarang</a>
                    </div>
                @endif

                <!-- Voucher Detail Modal -->
                <div x-show="showVoucherModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showVoucherModal = false">
                    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-xl" @click.stop>
                        <!-- Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="selectedVoucher?.name"></h3>
                            <button @click="showVoucherModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-4">
                            <!-- Image -->
                            <template x-if="selectedVoucher?.image_url">
                                <img :src="selectedVoucher.image_url" :alt="selectedVoucher.name" class="w-full h-40 object-cover rounded-lg">
                            </template>

                            <!-- Details -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tipe Voucher</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <span x-show="selectedVoucher?.type === 'fixed'" x-text="`Potongan Harga: Rp${formatPrice(selectedVoucher?.value)}`"></span>
                                        <span x-show="selectedVoucher?.type === 'percent'" x-text="`Diskon: ${selectedVoucher?.value}%`"></span>
                                        <template x-if="selectedVoucher?.type === 'percent' && selectedVoucher?.maximum_discount">
                                            <span x-text=`(Max: Rp${formatPrice(selectedVoucher?.maximum_discount)})`"></span>
                                        </template>
                                        <span x-show="selectedVoucher?.type === 'free_shipping'">Gratis Ongkir</span>
                                    </p>
                                </div>

                                <div x-show="selectedVoucher?.minimum_purchase > 0">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Minimal Pembelian</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Rp<span x-text="formatPrice(selectedVoucher?.minimum_purchase)"></span>
                                    </p>
                                </div>

                                <div x-show="selectedVoucher?.quota">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Sisa Kuota</p>
                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="`${selectedVoucher?.remaining_quota || 0} dari ${selectedVoucher?.quota}`"></p>
                                </div>

                                <div x-show="selectedVoucher?.end_at">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Berlaku Hingga</p>
                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="formatDate(selectedVoucher?.end_at)"></p>
                                </div>

                                <template x-if="selectedVoucher?.description">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Deskripsi</p>
                                        <p class="text-gray-900 dark:text-white" x-text="selectedVoucher?.description"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                            <button @click="applyVoucher(selectedVoucher?.id); showVoucherModal = false" type="button"
                                :disabled="!selectedVoucher?.can_apply"
                                :class="selectedVoucher?.can_apply ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'"
                                class="w-full px-4 py-2 rounded-lg font-medium transition">
                                Gunakan Voucher
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
    $cartItems = $cart->items->map(function($item) {
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product_name,
            'product_image_url' => $item->product->thumbnail ?? null,
            'quantity' => $item->quantity,
            'product_price' => $item->product_price,
            'original_price' => $item->original_price,
            'subtotal' => $item->subtotal,
            'discount_amount' => $item->discount_amount,
        ];
    })->toArray();

    $appliedVouchersArr = $cart->getAppliedVouchers()->map(function($v) {
        $va = (array) $v;
        return [
            'id' => $va['id'] ?? null,
            'name' => $va['name'] ?? null,
            'code' => $va['code'] ?? null,
            'description' => $va['description'] ?? null,
            'image_url' => $va['image_url'] ?? null,
            'type' => $va['type'] ?? null,
            'value' => $va['value'] ?? null,
            'minimum_purchase' => $va['minimum_purchase'] ?? 0,
        ];
    })->toArray();
?>

    <script>
        function cartApp() {
            return {
                items: @json($cartItems),
                voucherWarnings: [],
                
                summary: {
                    total_items: @json($cart->total_items),
                    total_quantity: @json($cart->total_quantity),
                    subtotal: @json($cart->subtotal),
                    discount_amount: @json($cart->total_discount_amount ?? 0),
                    final_subtotal: @json($cart->final_subtotal ?? $cart->subtotal - ($cart->total_discount_amount ?? 0)),
                },

                appliedVouchers: @json($appliedVouchersArr),

                availableVouchers: [],
                voucherWarnings: [],
                selectedVoucher: null,
                showVoucherModal: false,
                loading: false,
                loadingVouchers: false,

                init() {
                    this.loadAvailableVouchers();
                },

                async loadAvailableVouchers() {
                    this.loadingVouchers = true;
                    try {
                        const response = await fetch('{{ route('cart.vouchers.available') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.availableVouchers = data.data.map(v => ({
                                ...v,
                                remaining_quota: v.remaining_quota || v.quota
                            }));
                        }
                        // Ensure applied vouchers not duplicated
                        this.appliedVouchers = this.appliedVouchers.filter(av => this.isVoucherApplied(av.id));
                    } catch (error) {
                        console.error('Error loading vouchers:', error);
                    } finally {
                        this.loadingVouchers = false;
                    }
                },

                async updateQuantity(itemId, newQuantity) {
                    if (newQuantity < 1) return;
                    
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('cart.update', ':id') }}`.replace(':id', itemId), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ quantity: newQuantity })
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            this.updateCartData(data.data);
                            await this.loadAppliedVouchers();
                            this.loadAvailableVouchers();
                        }
                    } catch (error) {
                        console.error('Error updating quantity:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async removeItem(itemId) {
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('cart.remove', ':id') }}`.replace(':id', itemId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.items = this.items.filter(i => i.id !== itemId);
                            this.updateCartData(data.data);
                        await this.loadAppliedVouchers();
                        this.loadAvailableVouchers();
                        document.getElementById('item-count').textContent = this.summary.total_items;
                        }
                    } catch (error) {
                        console.error('Error removing item:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async applyVoucher(voucherId) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('cart.vouchers.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ voucher_id: voucherId })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.appliedVouchers = data.data.vouchers.map(v => ({
                                id: v.id,
                                name: v.name,
                                code: v.code,
                                description: v.description || null,
                                image_url: v.image_url || null,
                                type: v.type || null,
                                value: v.value || null,
                                minimum_purchase: v.minimum_purchase || 0,
                            }));
                            this.updateCartData(data.data);
                            await this.loadAppliedVouchers();
                            this.loadAvailableVouchers();
                        }
                    } catch (error) {
                        console.error('Error applying voucher:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async removeVoucher(voucherId) {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('cart.vouchers.remove') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ voucher_id: voucherId })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.appliedVouchers = data.data.vouchers.map(v => ({
                                id: v.id,
                                name: v.name,
                                code: v.code,
                                description: v.description || null,
                                image_url: v.image_url || null,
                                type: v.type || null,
                                value: v.value || null,
                                minimum_purchase: v.minimum_purchase || 0,
                            }));
                            this.updateCartData(data.data);
                            await this.loadAppliedVouchers();
                            this.loadAvailableVouchers();
                        }
                    } catch (error) {
                        console.error('Error removing voucher:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                showVoucherDetail(voucher) {
                    this.selectedVoucher = voucher;
                    this.showVoucherModal = true;
                },

                async loadAppliedVouchers() {
                    try {
                        const resp = await fetch('{{ route('cart.vouchers.current') }}', { headers: { 'Accept': 'application/json' } });
                        const res = await resp.json();
                        if (res.success) {
                            this.appliedVouchers = (res.data.vouchers || []).map(v => ({
                                id: v.id,
                                name: v.name,
                                code: v.code,
                                description: v.description || null,
                                image_url: v.image_url || null,
                                type: v.type || null,
                                value: v.value || null,
                                minimum_purchase: v.minimum_purchase || 0,
                            }));
                        }
                    } catch (e) {
                        console.error('Error loading applied vouchers', e);
                    }
                },

                updateCartData(data) {
                    if (data.items) {
                        this.items = data.items;
                    }
                    if (data.summary) {
                        this.summary = data.summary;
                        document.getElementById('item-count').textContent = data.summary.total_items;
                    }

                    // If server reports applied vouchers or invalid vouchers (from revalidation), update UI
                    if (data.applied_vouchers) {
                        this.appliedVouchers = (data.applied_vouchers || []).map(v => ({
                            id: v.id,
                            name: v.name,
                            code: v.code,
                            description: v.description || null,
                            image_url: v.image_url || null,
                            type: v.type || null,
                            value: v.value || null,
                            minimum_purchase: v.minimum_purchase || 0,
                        }));
                    }

                    if (data.invalid_vouchers) {
                        this.voucherWarnings = data.invalid_vouchers.map(v => v.reason || 'Voucher tidak memenuhi syarat');
                        // remove warnings after 6s
                        setTimeout(() => { this.voucherWarnings = []; }, 6000);
                    }
                },

                formatPrice(value) {
                    return new Intl.NumberFormat('id-ID').format(value || 0);
                },

                formatDate(date) {
                    if (!date) return '';
                    return new Date(date).toLocaleDateString('id-ID', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },

                isVoucherApplied(voucherId) {
                    return this.appliedVouchers.some(v => v.id === voucherId);
                }
            }
        }
    </script>
</x-layout>
