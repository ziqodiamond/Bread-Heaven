<!-- Cart Voucher Section - Alpine.js Version -->
@props(['appliedVouchers' => [], 'cartSummary' => []])

<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div x-data="cartVoucherManager()" x-init="init()" class="space-y-4">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">💳 Voucher & Promo</h3>
            <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-600">
                Maksimal 2
            </span>
        </div>

        <!-- Applied Vouchers -->
        <template x-if="applied.length > 0">
            <div class="space-y-2">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Voucher Teraplikasi:</p>
                <template x-for="v in applied" :key="v.id">
                    <div class="flex items-center justify-between rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-green-700 dark:text-green-300" x-text="'✓ ' + v.name"></p>
                            <p class="text-xs text-green-600 dark:text-green-400">Kode: <span class="font-mono" x-text="v.code"></span></p>
                        </div>
                        <button 
                            @click="remove(v.id)"
                            type="button"
                            class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <!-- Input Section -->
        <div class="space-y-3">
            <div class="flex gap-2">
                <input 
                    type="text" 
                    x-model="code"
                    @keyup.enter="apply()"
                    placeholder="Masukkan kode voucher"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <button
                    @click="apply()"
                    type="button"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Terapkan
                </button>
            </div>

            <!-- Error -->
            <template x-if="error">
                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                    <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
                </div>
            </template>

            <!-- Success -->
            <template x-if="success">
                <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                    <p class="text-sm text-green-700 dark:text-green-300" x-text="success"></p>
                </div>
            </template>
        </div>

        <!-- Available Vouchers -->
        <div class="border-t pt-4 dark:border-gray-700">
            <p class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Promo Tersedia:</p>
            
            <!-- Loading -->
            <template x-if="loading">
                <div class="text-center py-4">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Memuat voucher...</p>
                    </div>
                </div>
            </template>

            <!-- Carousel -->
            <template x-if="!loading && available.length > 0">
                <div class="relative">
                    <div class="flex gap-3 overflow-x-auto pb-2 snap-x">
                        <template x-for="v in available" :key="v.id">
                            <div class="flex-shrink-0 w-64 snap-start">
                                <div class="rounded-lg border border-gray-200 p-3 bg-gradient-to-br from-blue-50 to-white dark:border-gray-700 dark:from-gray-700 dark:to-gray-800">
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white" x-text="v.name"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300" x-text="v.description || ''"></p>
                                        
                                        <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                            <div>
                                                <strong x-text="v.type_label"></strong>: 
                                                <span x-text="'Rp' + formatNumber(v.value)"></span>
                                                <template x-if="v.maximum_discount">
                                                    <span x-text="' (Maks: Rp' + formatNumber(v.maximum_discount) + ')'"></span>
                                                </template>
                                            </div>
                                            <template x-if="v.minimum_purchase">
                                                <div x-text="'Min: Rp' + formatNumber(v.minimum_purchase)"></div>
                                            </template>
                                        </div>
                                        
                                        <button
                                            type="button"
                                            @click="copy(v.code)"
                                            class="w-full rounded bg-blue-600 px-2 py-2 text-xs font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors">
                                            📋 Salin: <span x-text="v.code"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Empty -->
            <template x-if="!loading && available.length === 0">
                <div class="text-center py-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada voucher tersedia</p>
                </div>
            </template>
        </div>

        <!-- Discount Summary -->
        <div class="border-t pt-4 dark:border-gray-700 space-y-2">
            <template x-if="discountSummary.total_discount > 0">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Potongan Diskon:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="'-Rp' + formatNumber(discountSummary.total_discount)"></span>
                </div>
            </template>

            <template x-if="discountSummary.total_shipping_discount > 0">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Gratis Ongkir:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="'-Rp' + formatNumber(discountSummary.total_shipping_discount)"></span>
                </div>
            </template>
        </div>
    </div>

    <script>
        function cartVoucherManager() {
            return {
                code: '',
                loading: true,
                error: null,
                success: null,
                applied: @json($appliedVouchers ?? []),
                available: [],
                discountSummary: @json($cartSummary ?? []),

                async init() {
                    await this.loadVouchers();
                },

                async loadVouchers() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const response = await fetch('{{ route("cart.vouchers.available") }}');
                        const data = await response.json();
                        if (data.success) {
                            this.available = data.data || [];
                        } else {
                            this.error = 'Gagal memuat voucher';
                        }
                    } catch (err) {
                        this.error = 'Terjadi kesalahan saat memuat voucher';
                        console.error(err);
                    } finally {
                        this.loading = false;
                    }
                },

                async apply() {
                    if (!this.code.trim()) {
                        this.error = 'Masukkan kode voucher terlebih dahulu';
                        return;
                    }
                    
                    this.error = null;
                    this.success = null;
                    this.loading = true;

                    try {
                        const response = await fetch('{{ route("cart.vouchers.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({ voucher_code: this.code.trim() })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.success = data.message || 'Voucher berhasil diterapkan';
                            this.code = '';
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            this.error = data.message || 'Gagal menerapkan voucher';
                        }
                    } catch (err) {
                        this.error = 'Terjadi kesalahan';
                        console.error(err);
                    } finally {
                        this.loading = false;
                    }
                },

                copy(code) {
                    navigator.clipboard.writeText(code).then(() => {
                        this.code = code;
                        this.success = `Kode '${code}' disalin!`;
                        setTimeout(() => this.success = null, 2000);
                    });
                },

                async remove(id) {
                    if (!confirm('Hapus voucher ini?')) return;

                    this.loading = true;
                    this.error = null;

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
                            this.success = 'Voucher berhasil dihapus';
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            this.error = data.message || 'Gagal menghapus voucher';
                        }
                    } catch (err) {
                        this.error = 'Terjadi kesalahan';
                        console.error(err);
                    } finally {
                        this.loading = false;
                    }
                },

                formatNumber(num) {
                    return (num || 0).toLocaleString('id-ID');
                }
            }
        }
    </script>
</div>

<script>
async function loadAvailableVouchers() {
    try {
        const response = await fetch('{{ route("cart.vouchers.available") }}');
        const result = await response.json();
        
        if (result.success && result.data) {
            const carousel = document.getElementById('vouchers-carousel');
            carousel.innerHTML = '';
            
            result.data.forEach(voucher => {
                const voucherCard = document.createElement('div');
                voucherCard.className = 'flex-shrink-0 w-64 snap-start';
                voucherCard.innerHTML = `
                    <div class="rounded-lg border border-gray-200 p-3 bg-gradient-to-br from-${voucher.badge_color || 'blue'}-50 to-white dark:border-gray-700 dark:bg-gray-700">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    ${voucher.name}
                                </h4>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                    ${voucher.description || ''}
                                </p>
                            </div>
                            ${voucher.is_sold_out ? '<span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-900 dark:text-red-200">Habis</span>' : ''}
                        </div>
                        
                        <div class="mt-3 space-y-2">
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                <strong>${voucher.type_label}</strong>: Rp${voucher.value.toLocaleString('id-ID')}
                                ${voucher.maximum_discount ? `(Maks: Rp${voucher.maximum_discount.toLocaleString('id-ID')})` : ''}
                            </div>
                            
                            ${voucher.minimum_purchase ? `<div class="text-xs text-gray-600 dark:text-gray-400">Min: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}</div>` : ''}
                            
                            <button
                                type="button"
                                onclick="copyVoucherCode('${voucher.code}')"
                                class="w-full rounded bg-blue-600 px-2 py-2 text-xs font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
                            >
                                📋 Salin: ${voucher.code}
                            </button>
                        </div>
                    </div>
                `;
                carousel.appendChild(voucherCard);
            });
        }
    } catch (error) {
        console.error('Error loading vouchers:', error);
    }
}

function copyVoucherCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const input = document.getElementById('voucher-input');
        input.value = code;
        
        // Tampilkan notif
        const notification = document.getElementById('voucher-success');
        notification.textContent = `Kode '${code}' disalin ke input!`;
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    });
}

async function applyVoucher() {
    const code = document.getElementById('voucher-input').value.trim();
    const errorEl = document.getElementById('voucher-error');
    const successEl = document.getElementById('voucher-success');
    
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');
    
    if (!code) {
        errorEl.textContent = 'Masukkan kode voucher terlebih dahulu!';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch('{{ route("cart.vouchers.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ voucher_code: code })
        });

        const result = await response.json();

        if (result.success) {
            successEl.textContent = result.message;
            successEl.classList.remove('hidden');
            document.getElementById('voucher-input').value = '';
            
            // Reload page or update cart summary
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            errorEl.textContent = result.message;
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = 'Terjadi kesalahan saat menerapkan voucher!';
        errorEl.classList.remove('hidden');
        console.error('Error:', error);
    }
}

async function removeVoucher(voucherId) {
    if (!confirm('Hapus voucher ini?')) return;

    try {
        const response = await fetch('{{ route("cart.vouchers.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ voucher_id: voucherId })
        });

        const result = await response.json();

        if (result.success) {
            location.reload();
        } else {
            alert('Gagal menghapus voucher!');
        }
    } catch (error) {
        alert('Terjadi kesalahan!');
        console.error('Error:', error);
    }
}

// Load available vouchers on page load
document.addEventListener('DOMContentLoaded', loadAvailableVouchers);
</script>
