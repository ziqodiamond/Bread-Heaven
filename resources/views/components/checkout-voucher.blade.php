<!-- Checkout Voucher Section - Alpine.js with Optimized Performance -->
@props(['appliedVouchers' => [], 'subtotal' => 0])

<div x-data="checkoutVoucherManager()" x-init="initialize()" @class="['w-full', 'space-y-4']">
    <!-- Applied Vouchers Section -->
    <div x-show="applied.length > 0" class="space-y-2">
        <div class="flex items-center justify-between">
            <label class="text-sm font-semibold text-green-700 dark:text-green-400">✓ Voucher Teraplikasi</label>
            <span class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded" x-text="applied.length + '/2'"></span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <template x-for="voucher in applied" :key="voucher.id">
                <div class="flex items-center justify-between p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-green-700 dark:text-green-300" x-text="voucher.name"></p>
                        <p class="text-xs text-green-600 dark:text-green-400 font-mono truncate" x-text="voucher.code"></p>
                    </div>
                    <button 
                        @click="removeVoucher(voucher.id)"
                        type="button"
                        class="ml-3 flex-shrink-0 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Search Input -->
    <div class="flex gap-2">
        <input 
            x-model="search"
            @keyup.enter="applyVoucherByCode()"
            type="text"
            placeholder="Cari atau ketik kode voucher..."
            class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
        >
        <button
            @click="applyVoucherByCode()"
            type="button"
            class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
        >
            Cari
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center py-8">
        <div class="text-center">
            <div class="inline-block animate-spin mb-2">
                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Memuat voucher...</p>
        </div>
    </div>

    <!-- Voucher Cards Grid -->
    <div x-show="!loading && filtered.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="voucher in filtered" :key="voucher.id">
            <div 
                @click="!voucher.can_apply ? null : selectVoucher(voucher)"
                :class="{
                    'opacity-50 cursor-not-allowed': !voucher.can_apply,
                    'cursor-pointer hover:shadow-md hover:-translate-y-0.5': voucher.can_apply,
                    'border-green-400 bg-green-50 dark:bg-green-900/20': isApplied(voucher.id)
                }"
                class="flex h-32 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden transition-all duration-200"
            >
                <!-- Image -->
                <div class="w-32 h-32 flex-shrink-0 overflow-hidden bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800">
                    <img :src="getImageUrl(voucher.image_path)" :alt="voucher.name" class="w-full h-full object-cover">
                </div>

                <!-- Content -->
                <div class="flex-1 p-4 flex flex-col justify-between min-w-0">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2" x-text="voucher.name"></h3>
                            <template x-if="voucher.is_sold_out">
                                <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-200 flex-shrink-0">Habis</span>
                            </template>
                            <template x-if="isApplied(voucher.id)">
                                <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-200 flex-shrink-0">✓</span>
                            </template>
                        </div>
                        
                        <div class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5">
                            <p class="font-semibold text-blue-600 dark:text-blue-400" x-text="`${voucher.type_label}: Rp${voucher.value.toLocaleString('id-ID')}`"></p>
                            <template x-if="voucher.minimum_purchase">
                                <p x-text="`💰 Min: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}`"></p>
                            </template>
                        </div>
                    </div>

                    <!-- Quota -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Quota</span>
                            <span class="text-gray-600 dark:text-gray-400" x-text="voucher.remaining_quota || 0"></span>
                        </div>
                        <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-300"
                                :style="`width: ${Math.min(100, (voucher.remaining_quota / (voucher.quota || 100)) * 100)}%`"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col items-center justify-center pr-2 gap-1">
                    <button 
                        @click.stop="showDetail(voucher)"
                        type="button"
                        class="p-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                        title="Detail"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    
                    <button 
                        @click.stop="copyCode(voucher.code)"
                        type="button"
                        class="p-1 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 transition-colors"
                        title="Copy"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && filtered.length === 0" class="text-center py-8">
        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="search ? 'Tidak ada voucher yang cocok' : 'Tidak ada voucher tersedia'"></p>
    </div>

    <!-- Error State -->
    <div x-show="error" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
        <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
    </div>

    <!-- Modal -->
    <div 
        x-show="detail"
        @click.self="detail = null"
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-96 overflow-y-auto" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="detail?.name"></h3>
                <button @click="detail = null" type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Image -->
                <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 aspect-square">
                    <img :src="getImageUrl(detail?.image_path)" :alt="detail?.name" class="w-full h-full object-cover">
                </div>

                <!-- Description -->
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-400">Deskripsi</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1" x-text="detail?.description || 'Tidak ada deskripsi'"></p>
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">Tipe</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1" x-text="detail?.type_label"></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">Nilai</label>
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-1" x-text="`Rp${detail?.value?.toLocaleString('id-ID')}`"></p>
                    </div>
                </div>

                <!-- Terms -->
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-400">Syarat & Ketentuan</label>
                    <ul class="text-xs text-gray-700 dark:text-gray-300 mt-2 space-y-1 list-disc list-inside">
                        <template x-if="detail?.minimum_purchase">
                            <li x-text="`Minimum: Rp${detail.minimum_purchase.toLocaleString('id-ID')}`"></li>
                        </template>
                        <template x-if="detail?.maximum_discount">
                            <li x-text="`Maksimum: Rp${detail.maximum_discount.toLocaleString('id-ID')}`"></li>
                        </template>
                        <template x-if="detail?.remaining_quota">
                            <li x-text="`Sisa Quota: ${detail.remaining_quota}`"></li>
                        </template>
                        <li x-text="`Bisa dikombinasi: ${detail?.is_combinable ? 'Ya' : 'Tidak'}`"></li>
                    </ul>
                </div>

                <!-- Reasons -->
                <template x-if="detail && !detail.can_apply && detail.reasons?.length > 0">
                    <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-300 mb-2">⚠️ Tidak bisa digunakan:</p>
                        <ul class="text-xs text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                            <template x-for="reason in detail.reasons" :key="reason">
                                <li x-text="reason"></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="flex gap-2 p-6 border-t dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800">
                <button @click="detail = null" type="button" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                    Tutup
                </button>
                <button 
                    @click="selectVoucher(detail)"
                    :disabled="!detail?.can_apply"
                    :class="{'opacity-50 cursor-not-allowed': !detail?.can_apply}"
                    type="button"
                    class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:bg-gray-400"
                >
                    <span x-text="detail?.can_apply ? 'Terapkan' : 'Tidak Bisa'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function checkoutVoucherManager() {
            return {
                loading: true,
                search: '',
                all: [],
                applied: @json($appliedVouchers ?? []),
                filtered: [],
                detail: null,
                error: null,

                async initialize() {
                    await this.fetchVouchers();
                    this.$watch('search', () => this.filterVouchers());
                },

                async fetchVouchers() {
                    try {
                        this.error = null;
                        const response = await fetch('{{ route("cart.vouchers.available") }}', {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const { success, data } = await response.json();
                        if (!success || !Array.isArray(data)) {
                            throw new Error('Invalid response');
                        }

                        this.all = data;
                        this.filterVouchers();
                    } catch (err) {
                        console.error('Fetch error:', err);
                        this.error = 'Gagal memuat voucher';
                        this.all = [];
                    } finally {
                        this.loading = false;
                    }
                },

                filterVouchers() {
                    const q = this.search.toLowerCase();
                    this.filtered = q 
                        ? this.all.filter(v => 
                            v.name.toLowerCase().includes(q) ||
                            v.code.toLowerCase().includes(q) ||
                            v.description.toLowerCase().includes(q)
                        )
                        : this.all;
                },

                isApplied(id) {
                    return this.applied.some(v => v.id === id);
                },

                getImageUrl(path) {
                    if (!path) return '/images/placeholder.png';
                    return path.startsWith('http') ? path : '/storage/' + path;
                },

                async selectVoucher(v) {
                    if (!v?.can_apply) return;
                    this.detail = null;
                    await this.applyVoucher(v.code);
                },

                async applyVoucher(code) {
                    try {
                        const response = await fetch('{{ route("cart.vouchers.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({ voucher_code: code })
                        });

                        const { success, message } = await response.json();
                        if (success) {
                            this.showToast('✓ ' + message, 'success');
                            setTimeout(() => location.reload(), 500);
                        } else {
                            this.showToast(message, 'error');
                        }
                    } catch (err) {
                        console.error('Apply error:', err);
                        this.showToast('Gagal menerapkan voucher', 'error');
                    }
                },

                async applyVoucherByCode() {
                    if (!this.search.trim()) return;
                    const voucher = this.all.find(v => v.code === this.search.trim());
                    if (voucher) {
                        await this.selectVoucher(voucher);
                    } else {
                        this.showToast('Voucher tidak ditemukan', 'error');
                    }
                },

                async removeVoucher(id) {
                    if (!confirm('Hapus voucher ini?')) return;
                    try {
                        const response = await fetch('{{ route("cart.vouchers.remove") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({ voucher_id: id })
                        });

                        const { success, message } = await response.json();
                        if (success) {
                            this.showToast('✓ ' + message, 'success');
                            setTimeout(() => location.reload(), 500);
                        } else {
                            this.showToast(message, 'error');
                        }
                    } catch (err) {
                        console.error('Remove error:', err);
                        this.showToast('Gagal menghapus voucher', 'error');
                    }
                },

                showDetail(voucher) {
                    this.detail = voucher;
                },

                copyCode(code) {
                    navigator.clipboard.writeText(code)
                        .then(() => {
                            this.search = code;
                            this.showToast('✓ Kode disalin!', 'success');
                        })
                        .catch(() => this.showToast('Gagal menyalin', 'error'));
                },

                showToast(msg, type = 'info') {
                    const div = document.createElement('div');
                    div.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-sm z-40 text-white ${
                        type === 'success' ? 'bg-green-600' : 'bg-red-600'
                    }`;
                    div.textContent = msg;
                    document.body.appendChild(div);
                    setTimeout(() => div.remove(), 3000);
                }
            }
        }
    </script>
</div>
