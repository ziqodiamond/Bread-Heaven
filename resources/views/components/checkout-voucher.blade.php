<!-- Checkout Voucher Section - Alpine.js with Optimized Performance -->
@props(['appliedVouchers' => [], 'subtotal' => 0])

<div x-data="checkoutVoucherManager()" x-init="initialize()" @class="['w-full', 'space-y-4']">

    {{-- ============================================================ --}}
    {{-- Style khusus untuk bentuk tiket (notch + ribbon quota)        --}}
    {{-- Warna notch disesuaikan dengan background halaman/card        --}}
    {{-- ============================================================ --}}
    <style>
        .voucher-ticket {
            position: relative;
            overflow: hidden;
        }

        .voucher-ticket .ticket-divider {
            position: relative;
            align-self: stretch;
            width: 0;
            border-left: 2px dashed rgba(148, 163, 184, .5);
            flex-shrink: 0;
        }

        .voucher-ticket .ticket-divider::before,
        .voucher-ticket .ticket-divider::after {
            content: '';
            position: absolute;
            left: -7px;
            width: 16px;
            height: 16px;
            border-radius: 9999px;
            background: #fff;
            /* samakan dengan warna background di belakang card */
        }

        .dark .voucher-ticket .ticket-divider::before,
        .dark .voucher-ticket .ticket-divider::after {
            background: #1f2937;
            /* dark:bg-gray-800 */
        }

        .voucher-ticket .ticket-divider::before {
            top: -8px;
        }

        .voucher-ticket .ticket-divider::after {
            bottom: -8px;
        }

        .voucher-ticket .ticket-ribbon {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 78%, 0 100%);
        }
    </style>

    <!-- Applied Vouchers Section -->
    <div x-show="applied.length > 0" class="space-y-2">
        <div class="flex items-center justify-between">
            <label class="text-sm font-semibold text-green-700 dark:text-green-400">✓ Voucher Teraplikasi</label>
            <span class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded"
                x-text="applied.length + '/2'"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <template x-for="voucher in applied" :key="voucher.id">
                <div
                    class="voucher-ticket flex h-32 rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 shadow-sm">
                    <!-- Flap kiri: icon + kategori -->
                    <div
                        class="w-24 md:w-28 flex-shrink-0 bg-emerald-600 flex flex-col items-center justify-center gap-2 px-2 py-3">
                        <div class="w-10 h-10 rounded-lg bg-white/90 flex items-center justify-center overflow-hidden">
                            <img :src="getImageUrl(voucher.image_path)" :alt="voucher.name"
                                class="w-6 h-6 object-contain" onerror="this.src='/images/placeholder.png'">
                        </div>
                        <span class="text-[10px] font-bold text-white text-center uppercase leading-tight line-clamp-2"
                            x-text="voucher.category_label ?? 'Semua Kategori'"></span>
                    </div>

                    <!-- Garis putus-putus + notch -->
                    <div class="ticket-divider"></div>

                    <!-- Konten -->
                    <div class="flex-1 min-w-0 p-3 md:p-4 flex flex-col justify-between">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1"
                                x-text="voucher.name"></h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate mt-0.5"
                                x-text="voucher.code"></p>
                        </div>

                        <div class="flex items-end justify-between gap-2">
                            <template x-if="voucher.valid_until">
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                    Berlaku Hingga: <span x-text="formatValidUntil(voucher.valid_until)"></span>
                                    <a href="#" @click.stop
                                        class="text-blue-600 dark:text-blue-400 font-semibold">S&K</a>
                                </p>
                            </template>

                            <button @click="removeVoucher(voucher.id)" type="button"
                                class="shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full border border-red-400 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Search Input -->
    <div class="flex gap-2">
        <input x-model="search" @keyup.enter="applyVoucherByCode()" type="text"
            placeholder="Cari atau ketik kode voucher..."
            class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        <button @click="applyVoucherByCode()" type="button"
            class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            Cari
        </button>
    </div>

    <!-- Voucher Cards Grid (tersedia / belum dipakai) -->
    <div x-show="filtered.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <template x-for="voucher in filtered" :key="voucher.id">
            <div class="voucher-ticket flex h-36 rounded-2xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm transition-opacity"
                :class="{ 'opacity-60': !voucher.can_apply }">
                <!-- Badge kuota / habis -->
                <div class="absolute top-0 right-4 z-10">
                    <div class="ticket-ribbon px-2.5 pt-1 pb-2 text-[11px] font-bold text-white"
                        :class="voucher.user_remaining_quota === 0 ? 'bg-gray-400' : 'bg-rose-500'">
                        <span
                            x-text="voucher.user_remaining_quota === 0 ? 'Limit Tercapai' : ('x' + voucher.user_remaining_quota)"></span>
                    </div>
                </div>

                <!-- Flap kiri: icon + kategori -->
                <div
                    class="w-24 md:w-28 flex-shrink-0 bg-emerald-500 flex flex-col items-center justify-center gap-2 px-2 py-3">
                    <div class="w-10 h-10 rounded-lg bg-white/90 flex items-center justify-center overflow-hidden">
                        <img :src="getImageUrl(voucher.image_path)" :alt="voucher.name"
                            class="w-6 h-6 object-contain" @onerror="$el.src=getPlaceholderSvg()">
                    </div>
                    <span class="text-[10px] font-bold text-white text-center uppercase leading-tight line-clamp-2"
                        x-text="voucher.category_label ?? 'Semua Kategori'"></span>
                </div>

                <!-- Garis putus-putus + notch -->
                <div class="ticket-divider"></div>

                <!-- Konten -->
                <div class="flex-1 min-w-0 p-3 md:p-4 flex flex-col justify-between">
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1" x-text="voucher.name">
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5"
                            x-text="'Min. Blj Rp' + (voucher.minimum_purchase ?? 0).toLocaleString('id-ID')"></p>
                    </div>

                    <div class="flex items-end justify-between gap-2">
                        <div class="min-w-0">
                            <template x-if="voucher.valid_until">
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                    Berlaku Hingga: <span x-text="formatValidUntil(voucher.valid_until)"></span>
                                    <a href="#" @click.stop
                                        class="text-blue-600 dark:text-blue-400 font-semibold">S&K</a>
                                </p>
                            </template>

                            <!-- Icon detail & copy tetap ada, cuma dikecilin biar muat di layout tiket -->
                            <div class="flex items-center gap-2 mt-1">
                                <button @click.stop="showDetail(voucher)" type="button" title="Detail"
                                    class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button @click.stop="copyCode(voucher.code)" type="button" title="Salin kode"
                                    class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button @click.stop="selectVoucher(voucher)" :disabled="!voucher.can_apply" type="button"
                            class="shrink-0 text-xs font-semibold px-4 py-1.5 rounded-full border transition-colors"
                            :class="voucher.can_apply ?
                                'border-emerald-500 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' :
                                'border-gray-300 text-gray-400 cursor-not-allowed'">
                            Pakai
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="filtered.length === 0" class="text-center py-8">
        <p class="text-sm text-gray-600 dark:text-gray-400"
            x-text="search ? 'Tidak ada voucher yang cocok' : 'Tidak ada voucher tersedia'"></p>
    </div>

    <!-- Error State -->
    <div x-show="error" class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
        <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
    </div>

    <!-- Modal (tidak diubah) -->
    <div x-show="detail" @click.self="detail = null"
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-96 overflow-y-auto"
            @click.stop>
            <!-- Header -->
            <div
                class="flex items-center justify-between p-6 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="detail?.name"></h3>
                <button @click="detail = null" type="button"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Image -->
                <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 aspect-square">
                    <img :src="getImageUrl(detail?.image_path)" :alt="detail?.name"
                        class="w-full h-full object-cover" @onerror="$el.src=getPlaceholderSvg()">
                </div>

                <!-- Description -->
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-400">Deskripsi</label>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1"
                        x-text="detail?.description || 'Tidak ada deskripsi'"></p>
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">Tipe</label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1"
                            x-text="detail?.type_label"></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">Nilai</label>
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-1"
                            x-text="`Rp${detail?.value?.toLocaleString('id-ID')}`"></p>
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
                            <li x-text="`Sisa Stok: ${detail.remaining_quota}`"></li>
                        </template>
                        <template x-if="detail?.user_remaining_quota !== undefined">
                            <li
                                x-text="`Bisa Digunakan: ${detail.user_remaining_quota} dari ${detail.max_usage_per_user}x`">
                            </li>
                        </template>
                        <li x-text="`Bisa dikombinasi: ${detail?.is_combinable ? 'Ya' : 'Tidak'}`"></li>
                    </ul>
                </div>

                <!-- Reasons -->
                <template x-if="detail && !detail.can_apply && detail.validation_reasons?.length > 0">
                    <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-300 mb-2">⚠️ Tidak bisa digunakan:
                        </p>
                        <ul class="text-xs text-red-600 dark:text-red-400 space-y-1 list-disc list-inside">
                            <template x-for="reason in detail.validation_reasons" :key="reason">
                                <li x-text="reason"></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="flex gap-2 p-6 border-t dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800">
                <button @click="detail = null" type="button"
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                    Tutup
                </button>
                <button @click="selectVoucher(detail)" :disabled="!detail?.can_apply"
                    :class="{ 'opacity-50 cursor-not-allowed': !detail?.can_apply }" type="button"
                    class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:bg-gray-400">
                    <span x-text="detail?.can_apply ? 'Terapkan' : 'Tidak Bisa'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function checkoutVoucherManager() {
            return {
                search: '',
                all: [],
                applied: @json($appliedVouchers ?? []),
                filtered: [],
                detail: null,
                error: null,
                fetchController: null,
                searchTimeout: null,

                async initialize() {
                    await this.fetchVouchers();
                    this.$watch('search', () => this.debouncedFilter());
                },

                debouncedFilter() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.filterVouchers(), 300);
                },

                async fetchVouchers() {
                    // Cancel previous request if still pending
                    if (this.fetchController) {
                        this.fetchController.abort();
                    }
                    this.fetchController = new AbortController();

                    try {
                        this.error = null;
                        const response = await fetch('{{ route('cart.vouchers.available') }}', {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: this.fetchController.signal
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const {
                            success,
                            data
                        } = await response.json();
                        if (!success || !Array.isArray(data)) {
                            throw new Error('Invalid response');
                        }

                        this.all = data;
                        this.filterVouchers();
                    } catch (err) {
                        if (err.name === 'AbortError') {
                            console.debug('Fetch cancelled');
                            return;
                        }
                        console.error('Fetch error:', err);
                        this.error = 'Gagal memuat voucher';
                        this.all = [];
                    }
                },

                filterVouchers() {
                    const q = this.search.toLowerCase().trim();
                    this.filtered = q ?
                        this.all.filter(v =>
                            v.name.toLowerCase().includes(q) ||
                            v.code.toLowerCase().includes(q) ||
                            v.description.toLowerCase().includes(q)
                        ) :
                        this.all;
                },

                isApplied(id) {
                    return this.applied.some(v => v.id === id);
                },

                getPlaceholderSvg() {
                    return 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%22 y=%2250%22 font-size=%2212%22 fill=%22%239ca3af%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3E?%3C/text%3E%3C/svg%3E';
                },

                getImageUrl(path) {
                    if (!path) return this.getPlaceholderSvg();
                    return path.startsWith('http') ? path : '/storage/' + path;
                },

                // Helper tampilan saja, format tanggal jadi "26 Jul" - tidak menyentuh logic apply/remove
                formatValidUntil(value) {
                    if (!value) return null;
                    const date = new Date(value);
                    if (isNaN(date)) return value;
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short'
                    });
                },

                async selectVoucher(v) {
                    if (!v?.can_apply) return;
                    this.detail = null;
                    await this.applyVoucher(v.code);
                },

                async applyVoucher(code) {
                    try {
                        const response = await fetch('{{ route('cart.vouchers.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({
                                voucher_code: code
                            })
                        });

                        const {
                            success,
                            message
                        } = await response.json();
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
                        const response = await fetch('{{ route('cart.vouchers.remove') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            },
                            body: JSON.stringify({
                                voucher_id: id
                            })
                        });

                        const {
                            success,
                            message
                        } = await response.json();
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
