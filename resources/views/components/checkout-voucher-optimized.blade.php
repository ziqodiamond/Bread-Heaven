@props(['appliedVouchers' => [], 'subtotal' => 0, 'cartTotal' => 0])

<div x-data="improvedCheckoutVoucher()" x-init="initialize()" class="w-full space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">🎟️ Kode Promo & Voucher</h3>
        <span x-show="applied.length > 0" class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full font-semibold">
            <span x-text="applied.length"></span> / 2 Dipakai
        </span>
    </div>

    <!-- Applied Vouchers -->
    <div x-show="applied.length > 0" x-transition class="space-y-3">
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Voucher Aktif:</p>
        <div class="space-y-2">
            <template x-for="voucher in applied" :key="voucher.id">
                <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/20 border border-green-200 dark:border-green-700">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-green-700 dark:text-green-300" x-text="voucher.name"></p>
                        <p class="text-xs text-green-600 dark:text-green-400 font-mono truncate" x-text="`Kode: ${voucher.code}`"></p>
                    </div>
                    <div class="flex items-center gap-3 ml-3">
                        <span class="text-sm font-bold text-green-700 dark:text-green-300 whitespace-nowrap" x-text="`-Rp${voucher.discount?.toLocaleString('id-ID')}`"></span>
                        <button @click="removeVoucher(voucher.id)" type="button" class="flex-shrink-0 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Input Kode Voucher -->
    <div class="space-y-2">
        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Masukkan Kode Voucher</label>
        <div class="flex gap-2">
            <input x-model="voucherCode" @keyup.enter="applyByCode()" type="text" placeholder="Contoh: PROMO2024" class="flex-1 px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition uppercase tracking-wide">
            <button @click="applyByCode()" :disabled="!voucherCode.trim() || loading" :class="{'opacity-50 cursor-not-allowed': !voucherCode.trim() || loading}" type="button" class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded-lg transition-colors">
                <span x-show="!loading">Pakai</span>
                <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">Tekan Enter atau klik Pakai</p>
    </div>

    <!-- Messages -->
    <div x-show="error" x-transition class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
        <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
    </div>

    <div x-show="success" x-transition class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
        <p class="text-sm text-green-700 dark:text-green-300" x-text="success"></p>
    </div>

    <!-- Available Vouchers - Horizontal Slider -->
    <div class="space-y-3">
        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Atau Pilih Voucher Tersedia</label>
        
        <!-- Loading State -->
        <div x-show="loadingVouchers" class="flex justify-center py-8">
            <div class="text-center">
                <svg class="h-5 w-5 animate-spin text-blue-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-xs text-gray-600 dark:text-gray-400">Memuat voucher...</p>
            </div>
        </div>

        <!-- Slider Container -->
        <div x-show="!loadingVouchers && allVouchers.length > 0" class="relative">
            <div x-ref="slider" class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory" style="scroll-behavior: smooth;">
                <template x-for="voucher in allVouchers" :key="voucher.id">
                    <div class="flex-shrink-0 w-80 snap-center">
                <div class="relative h-40 rounded-xl overflow-hidden border-2 transition-all duration-200 shadow-sm hover:shadow-md bg-white dark:bg-gray-800" :class="{'border-green-400 ring-2 ring-green-300': isApplied(voucher.id), 'border-gray-200 dark:border-gray-700': !isApplied(voucher.id) && voucher.can_apply, 'border-red-200 dark:border-red-700 opacity-50': !voucher.can_apply}">
                            
                    <!-- Image Left (1:1) -->
                    <div class="absolute left-0 top-0 h-full w-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-r border-gray-200 dark:border-gray-600">
                        <template x-if="voucher.image_url">
                            <img :src="voucher.image_url" :alt="voucher.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!voucher.image_url">
                            <!-- Icon berbeda untuk setiap tipe voucher -->
                            <template x-if="voucher.discount_type === 'free_shipping'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </template>
                            <template x-if="voucher.discount_type === 'percent'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 0l8-8m-8 8l-8 8" />
                                </svg>
                            </template>
                            <template x-if="voucher.discount_type === 'fixed'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 0v8m0-8h-4m4 0h4" />
                                </svg>
                            </template>
                        </template>
                    </div>

                    <!-- Content Right -->
                    <div class="absolute left-40 top-0 right-0 h-full p-3 flex flex-col justify-between">
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold line-clamp-2 text-gray-900 dark:text-white" x-text="voucher.name"></h4>
                            <p class="text-xs font-mono uppercase tracking-wider text-gray-600 dark:text-gray-400" x-text="voucher.code"></p>
                                    
                            <!-- Tipe Voucher Badge -->
                            <div class="text-xs font-semibold py-0.5 px-1.5 rounded inline-block" :class="{
                                'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': voucher.discount_type === 'free_shipping',
                                'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400': voucher.discount_type === 'percent',
                                'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': voucher.discount_type === 'fixed'
                            }">
                                <span x-text="voucher.discount_type === 'free_shipping' ? '🚚 Ongkir Gratis' : voucher.discount_type === 'percent' ? '📊 Diskon %' : '💰 Potongan Harga'"></span>
                            </div>
                        </div>

                        <div class="space-y-0.5">
                            <div class="text-lg font-bold text-gray-900 dark:text-white" x-text="getVoucherValue(voucher)"></div>
                            <template x-if="voucher.minimum_purchase && voucher.minimum_purchase > 0">
                                <p class="text-xs text-gray-600 dark:text-gray-400" x-text="`Min: Rp${numberFormat(voucher.minimum_purchase)}`"></p>
                            </template>
                        </div>

                        <div class="flex gap-2">
                            <div x-show="isApplied(voucher.id)" class="flex-1 px-2 py-1 rounded bg-green-100 dark:bg-green-900/30 text-center">
                                <p class="text-xs font-semibold text-green-700 dark:text-green-400">✓ DIPAKAI</p>
                            </div>

                            <button x-show="!isApplied(voucher.id)" @click="applyVoucher(voucher)" :disabled="!voucher.can_apply" :class="{'bg-blue-600 text-white hover:bg-blue-700': voucher.can_apply, 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 cursor-not-allowed': !voucher.can_apply}" type="button" class="flex-1 px-2 py-1 text-xs font-semibold rounded transition-colors">
                                <span x-text="voucher.can_apply ? 'Pakai' : 'Tidak Bisa'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <template x-if="voucher.is_sold_out">
                        <div class="absolute top-2 right-2 px-2 py-1 rounded-full bg-red-500 text-white text-xs font-semibold">Habis</div>
                    </template>

                    <template x-if="!voucher.can_apply && voucher.reasons && voucher.reasons.length > 0">
                        <div class="absolute bottom-2 left-44 right-2 px-1 py-0.5 rounded bg-red-500/90 text-white text-xs font-semibold line-clamp-1" x-text="voucher.reasons[0]"></div>
                    </template>
                </div>
            </div>
        </template>
            </div>

            <!-- Scroll Buttons -->
            <button @click="$refs.slider.scrollBy({left: -320, behavior: 'smooth'})" x-show="allVouchers.length > 1" class="absolute -left-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button @click="$refs.slider.scrollBy({left: 320, behavior: 'smooth'})" x-show="allVouchers.length > 1" class="absolute -right-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Empty State -->
        <div x-show="!loadingVouchers && allVouchers.length === 0" class="text-center py-8">
            <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada voucher tersedia</p>
        </div>
    </div>

    <!-- Calculation Summary -->
    <div x-show="applied.length > 0" x-transition class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 space-y-2">
        <div class="flex justify-between text-xs">
            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
            <span class="font-semibold text-gray-900 dark:text-white" x-text="`Rp${numberFormat(subtotal)}`"></span>
        </div>
        <div class="flex justify-between text-xs">
            <span class="text-gray-600 dark:text-gray-400">Total Diskon:</span>
            <span class="font-semibold text-red-600 dark:text-red-400" x-text="`-Rp${numberFormat(totalDiscount)}`"></span>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between text-sm">
            <span class="font-semibold text-gray-900 dark:text-white">Total Pembayaran:</span>
            <span class="font-bold text-green-600 dark:text-green-400" x-text="`Rp${numberFormat(finalTotal)}`"></span>
        </div>
    </div>

    <script>
        function improvedCheckoutVoucher() {
            return {
                voucherCode: '',
                loading: false,
                loadingVouchers: true,
                error: '',
                success: '',
                applied: @json($appliedVouchers ?? []),
                allVouchers: [],
                subtotal: @json($subtotal ?? 0),

                async initialize() {
                    await this.fetchVouchers();
                },

                async fetchVouchers() {
                    try {
                        this.loadingVouchers = true;
                        const response = await fetch('{{ route("cart.vouchers.available") }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        const { success, data } = await response.json();
                        if (success && Array.isArray(data)) {
                            this.allVouchers = data;
                        }
                    } catch (err) {
                        console.error('Fetch error:', err);
                        this.error = 'Gagal memuat voucher';
                    } finally {
                        this.loadingVouchers = false;
                    }
                },

                // Format number dengan separator ribuan
                numberFormat(value) {
                    if (!value || isNaN(value)) return '0';
                    return parseInt(value).toLocaleString('id-ID');
                },

                // Get display value berdasarkan tipe voucher
                getVoucherValue(voucher) {
                    if (!voucher) return '-';
                    
                    if (voucher.discount_type === 'free_shipping') {
                        return '🚚 Ongkir Gratis';
                    } else if (voucher.discount_type === 'percent') {
                        const value = voucher.discount_value || 0;
                        return `${value}% Diskon`;
                    } else if (voucher.discount_type === 'fixed') {
                        const value = voucher.discount_value || 0;
                        return `Rp${this.numberFormat(value)} Potong`;
                    }
                    return '-';
                },

                formatDiscount(voucher) {
                    if (voucher.discount_type === 'percent') return `${voucher.discount_value}%`;
                    if (voucher.discount_type === 'fixed') return `Rp${this.numberFormat(voucher.discount_value)}`;
                    return 'Gratis Ongkir';
                },

                isApplied(id) {
                    return this.applied.some(v => v.id === id);
                },

                get totalDiscount() {
                    return this.applied.reduce((sum, v) => sum + (v.discount ?? 0), 0);
                },

                get finalTotal() {
                    return Math.max(0, this.subtotal - this.totalDiscount);
                },

                async applyByCode() {
                    if (!this.voucherCode.trim()) return;
                    this.error = '';
                    this.success = '';
                    const voucher = this.allVouchers.find(v => v.code === this.voucherCode.trim().toUpperCase());
                    if (!voucher) {
                        this.error = '❌ Kode voucher tidak ditemukan';
                        return;
                    }
                    if (!voucher.can_apply) {
                        this.error = `❌ ${voucher.reasons?.[0] || 'Voucher tidak bisa digunakan'}`;
                        return;
                    }
                    await this.applyVoucher(voucher);
                },

                async applyVoucher(voucher) {
                    if (!voucher.can_apply) {
                        this.error = `❌ ${voucher.reasons?.[0] || 'Voucher tidak bisa digunakan'}`;
                        return;
                    }
                    if (this.isApplied(voucher.id)) {
                        this.error = '❌ Voucher ini sudah dipakai';
                        return;
                    }
                    if (this.applied.length >= 2) {
                        this.error = '❌ Maksimal 2 voucher dapat digunakan sekaligus';
                        return;
                    }
                    if (voucher.minimum_purchase && this.subtotal < voucher.minimum_purchase) {
                        this.error = `❌ Minimum pembelian Rp${this.numberFormat(voucher.minimum_purchase)}`;
                        return;
                    }
                    this.loading = true;
                    this.error = '';
                    this.success = '';
                    try {
                        const response = await fetch('{{ route("cart.vouchers.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({ voucher_code: voucher.code })
                        });
                        const data = await response.json();
                        if (data.success) {
                            const discountAmount = this.calculateDiscount(voucher);
                            this.applied.push({
                                id: voucher.id,
                                name: voucher.name,
                                code: voucher.code,
                                discount: discountAmount
                            });
                            this.success = `✓ ${data.message}`;
                            this.voucherCode = '';
                            setTimeout(() => this.notifyCartUpdate(), 1000);
                        } else {
                            this.error = `❌ ${data.message}`;
                        }
                    } catch (err) {
                        console.error('Apply error:', err);
                        this.error = '❌ Gagal menerapkan voucher';
                    } finally {
                        this.loading = false;
                    }
                },

                calculateDiscount(voucher) {
                    if (voucher.discount_type === 'percent') {
                        const discountAmount = (this.subtotal * voucher.discount_value) / 100;
                        return Math.min(discountAmount, voucher.max_discount || Infinity);
                    }
                    return voucher.discount_value || 0;
                },

                async removeVoucher(id) {
                    if (!confirm('Hapus voucher ini?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("cart.vouchers.remove") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({ voucher_id: id })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.applied = this.applied.filter(v => v.id !== id);
                            this.success = `✓ ${data.message}`;
                            this.notifyCartUpdate();
                        } else {
                            this.error = `❌ ${data.message}`;
                        }
                    } catch (err) {
                        console.error('Remove error:', err);
                        this.error = '❌ Gagal menghapus voucher';
                    } finally {
                        this.loading = false;
                    }
                },

                notifyCartUpdate() {
                    window.dispatchEvent(new CustomEvent('voucherApplied', { 
                        detail: { 
                            applied: this.applied,
                            totalDiscount: this.totalDiscount
                        }
                    }));
                }
            }
        }
    </script>
</div>
