<!-- Advanced Voucher Section untuk Checkout - Alpine.js Version -->
@props(['appliedVouchers' => null, 'subtotal' => 0])

<div x-data="voucherManager()" x-init="init()" class="w-full">
    <!-- Applied Vouchers Display -->
    @if ($appliedVouchers && count($appliedVouchers) > 0)
        <div class="mb-6 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-green-700 dark:text-green-400">✓ Voucher Teraplikasi</p>
                <span class="text-xs text-green-600 dark:text-green-300" x-text="appliedCount + '/2'"></span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($appliedVouchers as $voucher)
                    <div class="flex items-center justify-between rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 p-4 border border-green-200 dark:from-green-900/30 dark:to-emerald-900/30 dark:border-green-700">
                        <div>
                            <p class="text-sm font-bold text-green-700 dark:text-green-300">{{ $voucher['name'] }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400 font-mono">{{ $voucher['code'] }}</p>
                        </div>
                        <button 
                            type="button"
                            @click="removeVoucher('{{ $voucher['id'] }}')"
                            class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Ticket-Style Voucher Cards -->
    <div class="mb-6">
        <p class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">🎁 Pilih Voucher</p>
        
        <!-- Search/Input -->
        <div class="mb-4 flex gap-2">
            <input 
                type="text" 
                x-model="searchQuery"
                @keyup.enter="filterVouchers()"
                placeholder="Cari atau ketik kode voucher..."
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
            <button 
                type="button"
                @click="filterVouchers()"
                class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                Cari
            </button>
        </div>

        <!-- Vouchers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Loading State -->
            <template x-if="loading">
                <div class="col-span-full text-center py-8">
                    <div class="inline-block">
                        <div class="animate-spin h-5 w-5 text-blue-600 mb-2"></div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Memuat voucher...</p>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && filteredVouchers.length === 0">
                <div class="col-span-full text-center py-8 text-sm text-gray-600 dark:text-gray-400">
                    Tidak ada voucher tersedia
                </div>
            </template>

            <!-- Voucher Cards -->
            <template x-if="!loading && filteredVouchers.length > 0">
                <template x-for="voucher in filteredVouchers" :key="voucher.id">
                    <div 
                        :class="['voucher-ticket', {
                            'disabled': !voucher.can_apply,
                            'selected': isApplied(voucher.id)
                        }]"
                        @click="!voucher.can_apply ? null : selectVoucher(voucher)"
                    >
                        <!-- Image -->
                        <div class="w-32 h-32 flex-shrink-0 overflow-hidden bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800">
                            <img :src="getImageUrl(voucher.image_path)" :alt="voucher.name" class="w-full h-full object-cover">
                        </div>

                        <!-- Content -->
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <!-- Header -->
                            <div>
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2" x-text="voucher.name"></h4>
                                    <template x-if="voucher.is_sold_out">
                                        <span class="ml-2 inline-flex rounded-full bg-red-100 dark:bg-red-900 px-2 py-0.5 text-xs font-semibold text-red-600 dark:text-red-200 flex-shrink-0">Habis</span>
                                    </template>
                                    <template x-if="isApplied(voucher.id)">
                                        <span class="ml-2 inline-flex rounded-full bg-green-100 dark:bg-green-900 px-2 py-0.5 text-xs font-semibold text-green-600 dark:text-green-200 flex-shrink-0">✓ Pakai</span>
                                    </template>
                                </div>
                                
                                <!-- Value & Minimum -->
                                <div class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5">
                                    <p class="font-semibold text-blue-600 dark:text-blue-400" x-text="`${voucher.type_label}: Rp${voucher.value.toLocaleString('id-ID')}`"></p>
                                    <template x-if="voucher.minimum_purchase">
                                        <p x-text="`💰 Min: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}`"></p>
                                    </template>
                                </div>
                            </div>

                            <!-- Quota Bar -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Quota</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400" x-text="voucher.remaining_quota || 0"></span>
                                </div>
                                <div class="quota-bar">
                                    <div 
                                        class="quota-bar-fill" 
                                        :style="`width: ${Math.min(100, (voucher.remaining_quota / (voucher.quota || 100)) * 100)}%; background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);`"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col items-center justify-center pr-2 gap-1">
                            <button 
                                type="button"
                                @click.stop="showModal(voucher.id)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 p-1 transition-colors"
                                title="Detail">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                            
                            <button 
                                type="button"
                                @click.stop="copyCode(voucher.code)"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 p-1 transition-colors"
                                title="Copy Kode">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    <!-- Modal Detail Voucher -->
    <div 
        x-show="modalOpen"
        @click.self="closeModal()"
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto"
        style="display: none;"
    >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full my-8">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="currentVoucher?.name"></h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                <!-- Image -->
                <div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 aspect-square w-full">
                    <img :src="getImageUrl(currentVoucher?.image_path)" :alt="currentVoucher?.name" class="w-full h-full object-cover">
                </div>

                <!-- Description -->
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="currentVoucher?.description || 'Tidak ada deskripsi'"></p>
                </div>

                <!-- Type & Value -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tipe</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="currentVoucher?.type_label"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nilai</p>
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400" x-text="`Rp${currentVoucher?.value?.toLocaleString('id-ID') || '0'}`"></p>
                    </div>
                </div>

                <!-- Terms -->
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Syarat & Ketentuan</p>
                    <ul class="space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc list-inside">
                        <template x-if="currentVoucher?.minimum_purchase">
                            <li x-text="`Minimum: Rp${currentVoucher.minimum_purchase.toLocaleString('id-ID')}`"></li>
                        </template>
                        <template x-if="currentVoucher?.maximum_discount">
                            <li x-text="`Maksimum: Rp${currentVoucher.maximum_discount.toLocaleString('id-ID')}`"></li>
                        </template>
                        <template x-if="currentVoucher?.remaining_quota">
                            <li x-text="`Sisa Quota: ${currentVoucher.remaining_quota}`"></li>
                        </template>
                        <li x-text="`Bisa dikombinasi: ${currentVoucher?.is_combinable ? 'Ya' : 'Tidak'}`"></li>
                    </ul>
                </div>

                <!-- Reasons (if not applicable) -->
                <template x-if="currentVoucher && !currentVoucher.can_apply && currentVoucher.reasons?.length > 0">
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-3 border border-red-200 dark:border-red-700">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-300 mb-1">⚠️ Tidak bisa digunakan:</p>
                        <ul class="space-y-1 text-xs text-red-600 dark:text-red-400 list-disc list-inside">
                            <template x-for="reason in currentVoucher.reasons" :key="reason">
                                <li x-text="reason"></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="flex gap-2 p-6 border-t dark:border-gray-700">
                <button @click="closeModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors">
                    Tutup
                </button>
                <button 
                    @click="applyFromModal()"
                    :disabled="!currentVoucher?.can_apply"
                    :class="['flex-1 px-4 py-2 text-sm font-semibold rounded-lg transition-colors', currentVoucher?.can_apply ? 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed']"
                    :class="{' pointer-events-none': !currentVoucher?.can_apply}"
                >
                    <span x-text="currentVoucher?.can_apply ? 'Terapkan' : 'Tidak Bisa Digunakan'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .voucher-ticket {
            @apply rounded-lg border-2 border-gray-200 overflow-hidden transition-all duration-200 dark:border-gray-700 flex h-32;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        }

        .voucher-ticket.dark {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        .voucher-ticket:hover:not(.disabled) {
            @apply shadow-md border-blue-400 dark:border-blue-600;
            transform: translateY(-2px);
        }

        .voucher-ticket.disabled {
            @apply opacity-50 cursor-not-allowed;
        }

        .voucher-ticket.selected {
            @apply border-green-400 shadow-md dark:border-green-600;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        }

        .voucher-ticket.dark.selected {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        }

        .quota-bar {
            @apply h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700;
        }

        .quota-bar-fill {
            @apply h-full rounded-full transition-all duration-300;
        }
    </style>

    <!-- Alpine.js Logic -->
    <script>
        function voucherManager() {
            return {
                loading: true,
                searchQuery: '',
                allVouchers: [],
                filteredVouchers: [],
                modalOpen: false,
                currentVoucher: null,
                appliedCount: @json(count($appliedVouchers ?? [])),
                subtotal: {{ $subtotal }},

                async init() {
                    await this.loadVouchers();
                    this.$watch('searchQuery', () => this.filterVouchers());
                },

                async loadVouchers() {
                    try {
                        this.loading = true;
                        const response = await fetch('{{ route("cart.vouchers.available") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        
                        const data = await response.json();
                        
                        if (data.success && Array.isArray(data.data)) {
                            this.allVouchers = data.data;
                            this.filteredVouchers = data.data;
                        } else {
                            throw new Error('Invalid response format');
                        }
                    } catch (error) {
                        console.error('Error loading vouchers:', error);
                        this.showError('Gagal memuat voucher. Silakan refresh halaman.');
                    } finally {
                        this.loading = false;
                    }
                },

                filterVouchers() {
                    const query = this.searchQuery.toLowerCase().trim();
                    if (!query) {
                        this.filteredVouchers = this.allVouchers;
                    } else {
                        this.filteredVouchers = this.allVouchers.filter(v => 
                            v.name?.toLowerCase().includes(query) ||
                            v.code?.toLowerCase().includes(query) ||
                            v.description?.toLowerCase().includes(query)
                        );
                    }
                },

                isApplied(voucherId) {
                    const applied = document.querySelectorAll('[onclick*="removeCheckoutVoucher"]');
                    return Array.from(applied).some(el => el.onclick.toString().includes(voucherId));
                },

                getImageUrl(imagePath) {
                    if (!imagePath) return '/images/voucher-placeholder.png';
                    if (imagePath.startsWith('http')) return imagePath;
                    return `{{ asset('') }}${imagePath}`;
                },

                async selectVoucher(voucher) {
                    if (!voucher.can_apply) return;
                    await this.applyVoucher(voucher.code);
                },

                async applyVoucher(code) {
                    try {
                        const response = await fetch('{{ route("cart.vouchers.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ voucher_code: code })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showSuccess(`✓ ${result.message}`);
                            this.closeModal();
                            setTimeout(() => location.reload(), 500);
                        } else {
                            this.showError(result.message || 'Gagal menerapkan voucher');
                        }
                    } catch (error) {
                        console.error('Error applying voucher:', error);
                        this.showError('Terjadi kesalahan. Silakan coba lagi.');
                    }
                },

                async removeVoucher(voucherId) {
                    if (!confirm('Hapus voucher ini?')) return;
                    
                    try {
                        const response = await fetch('{{ route("cart.vouchers.remove") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ voucher_id: voucherId })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.showSuccess('✓ Voucher berhasil dihapus');
                            setTimeout(() => location.reload(), 500);
                        }
                    } catch (error) {
                        console.error('Error removing voucher:', error);
                        this.showError('Gagal menghapus voucher');
                    }
                },

                showModal(voucherId) {
                    this.currentVoucher = this.allVouchers.find(v => v.id === voucherId);
                    if (this.currentVoucher) {
                        this.modalOpen = true;
                    }
                },

                closeModal() {
                    this.modalOpen = false;
                    this.currentVoucher = null;
                },

                async applyFromModal() {
                    if (this.currentVoucher?.can_apply) {
                        await this.applyVoucher(this.currentVoucher.code);
                    }
                },

                copyCode(code) {
                    navigator.clipboard.writeText(code).then(() => {
                        this.searchQuery = code;
                        this.showSuccess(`✓ Kode '${code}' disalin!`);
                    }).catch(() => {
                        this.showError('Gagal menyalin kode');
                    });
                },

                showSuccess(message) {
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-40';
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                },

                showError(message) {
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-40';
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
            }
        }
    </script>
</div>
    <!-- Applied Vouchers Display -->
    @if ($appliedVouchers && count($appliedVouchers) > 0)
        <div class="mb-6 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-green-700 dark:text-green-400">✓ Voucher Teraplikasi</p>
                <span class="text-xs text-green-600 dark:text-green-300">{{ count($appliedVouchers) }}/2</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($appliedVouchers as $voucher)
                    <div class="flex items-center justify-between rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 p-4 border border-green-200 dark:from-green-900/30 dark:to-emerald-900/30 dark:border-green-700">
                        <div>
                            <p class="text-sm font-bold text-green-700 dark:text-green-300">{{ $voucher['name'] }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400 font-mono">{{ $voucher['code'] }}</p>
                        </div>
                        <button 
                            type="button"
                            onclick="removeCheckoutVoucher('{{ $voucher['id'] }}')"
                            class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mb-6 rounded-lg bg-blue-50 p-4 text-center dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700">
            <p class="text-sm text-blue-600 dark:text-blue-300">Belum ada voucher diterapkan</p>
        </div>
    @endif

    <!-- Ticket-Style Voucher Cards -->
    <div class="mb-6">
        <p class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">🎁 Pilih Voucher</p>
        
        <!-- Search/Input -->
        <div class="mb-4 flex gap-2">
            <input 
                type="text" 
                id="checkout-voucher-search"
                placeholder="Cari atau ketik kode voucher..."
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
            <button 
                type="button"
                onclick="searchCheckoutVouchers()"
                class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                Cari
            </button>
        </div>

        <!-- Vouchers Grid -->
        <div id="checkout-vouchers-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div id="checkout-vouchers-loading" class="col-span-full text-center py-8">
                <div class="inline-block">
                    <div class="animate-spin h-5 w-5 text-blue-600 mb-2"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Memuat voucher...</p>
                </div>
            </div>
        </div>

        <p id="checkout-voucher-search-error" class="text-xs text-red-600 dark:text-red-400 hidden mt-2"></p>
    </div>
</div>

<!-- Modal Detail Voucher -->
<div id="voucherDetailModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Detail Voucher</h3>
            <button onclick="closeVoucherModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
            <div id="modalImage" class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 aspect-square w-full">
                <!-- Image will be inserted here -->
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                <p id="modalDescription" class="text-sm text-gray-700 dark:text-gray-300"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tipe</p>
                    <p id="modalType" class="text-sm font-semibold text-gray-900 dark:text-white"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nilai</p>
                    <p id="modalValue" class="text-sm font-semibold text-blue-600 dark:text-blue-400"></p>
                </div>
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Syarat & Ketentuan</p>
                <ul id="modalTerms" class="space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc list-inside">
                    <!-- Terms will be inserted here -->
                </ul>
            </div>

            <div id="modalReasons" class="rounded-lg bg-red-50 dark:bg-red-900/20 p-3 border border-red-200 dark:border-red-700 hidden">
                <p class="text-xs font-semibold text-red-700 dark:text-red-300 mb-1">⚠️ Tidak bisa digunakan:</p>
                <ul id="reasonsList" class="space-y-1 text-xs text-red-600 dark:text-red-400 list-disc list-inside">
                    <!-- Reasons will be inserted here -->
                </ul>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex gap-2 p-6 border-t dark:border-gray-700">
            <button onclick="closeVoucherModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors">
                Tutup
            </button>
            <button id="modalApplyBtn" onclick="applyVoucherFromModal()" class="flex-1 px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                Terapkan
            </button>
        </div>
    </div>
</div>

<style>
    .voucher-ticket {
        @apply rounded-lg border-2 border-gray-200 overflow-hidden transition-all duration-200 dark:border-gray-700;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    }

    .voucher-ticket.dark {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }

    .voucher-ticket:hover:not(.disabled) {
        @apply shadow-md border-blue-400 dark:border-blue-600;
        transform: translateY(-2px);
    }

    .voucher-ticket.disabled {
        @apply opacity-50 cursor-not-allowed;
    }

    .voucher-ticket.selected {
        @apply border-green-400 shadow-md dark:border-green-600;
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
    }

    .voucher-ticket.dark.selected {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    }

    .quota-bar {
        @apply h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700;
    }

    .quota-bar-fill {
        @apply h-full rounded-full transition-all duration-300;
    }

    .button-disabled {
        @apply opacity-50 cursor-not-allowed;
    }
</style>

<script>
let allVouchers = [];
let currentModalVoucherId = null;

// Load available vouchers saat page load
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutAdvancedVouchers();
});

async function loadCheckoutAdvancedVouchers() {
    try {
        const response = await fetch('{{ route("cart.vouchers.available") }}');
        const result = await response.json();
        
        if (result.success && result.data) {
            allVouchers = result.data;
            renderVoucherCards(allVouchers);
        }
    } catch (error) {
        console.error('Error loading vouchers:', error);
        document.getElementById('checkout-vouchers-loading').innerHTML = 
            '<div class="text-center text-sm text-red-600 dark:text-red-400">Gagal memuat voucher</div>';
    }
}

function renderVoucherCards(vouchers) {
    const grid = document.getElementById('checkout-vouchers-grid');
    grid.innerHTML = '';

    if (vouchers.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-8 text-sm text-gray-600 dark:text-gray-400">Tidak ada voucher tersedia</div>';
        return;
    }

    // Separate usable and not usable vouchers
    const usable = vouchers.filter(v => v.can_apply !== false);
    const notUsable = vouchers.filter(v => v.can_apply === false);

    // Render usable first
    [...usable, ...notUsable].forEach(voucher => {
        const voucherCard = document.createElement('div');
        voucherCard.className = `voucher-ticket ${voucher.can_apply === false ? 'disabled' : ''}`;
        voucherCard.innerHTML = getVoucherCardHTML(voucher);
        voucherCard.onclick = () => !voucher.can_apply === false ? selectVoucher(voucher) : null;
        grid.appendChild(voucherCard);
    });
}

function getVoucherCardHTML(voucher) {
    const percentQuota = voucher.remaining_quota ? Math.min(100, (voucher.remaining_quota / (voucher.quota || 100)) * 100) : 0;
    const imageUrl = voucher.image_path ? `{{ asset('') }}${voucher.image_path}` : '/images/voucher-placeholder.png';
    const isApplied = isVoucherApplied(voucher.id);
    
    return `
        <div class="flex h-32">
            <!-- Image -->
            <div class="w-32 h-32 flex-shrink-0 overflow-hidden bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800">
                <img src="${imageUrl}" alt="${voucher.name}" class="w-full h-full object-cover">
            </div>

            <!-- Content -->
            <div class="flex-1 p-4 flex flex-col justify-between">
                <!-- Header -->
                <div>
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2">
                            ${voucher.name}
                        </h4>
                        ${voucher.is_sold_out ? '<span class="ml-2 inline-flex rounded-full bg-red-100 dark:bg-red-900 px-2 py-0.5 text-xs font-semibold text-red-600 dark:text-red-200 flex-shrink-0">Habis</span>' : ''}
                        ${isApplied ? '<span class="ml-2 inline-flex rounded-full bg-green-100 dark:bg-green-900 px-2 py-0.5 text-xs font-semibold text-green-600 dark:text-green-200 flex-shrink-0">✓ Pakai</span>' : ''}
                    </div>
                    
                    <!-- Value & Minimum -->
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5">
                        <p class="font-semibold text-blue-600 dark:text-blue-400">${voucher.type_label}: Rp${voucher.value.toLocaleString('id-ID')}</p>
                        ${voucher.minimum_purchase ? `<p>💰 Min: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}</p>` : ''}
                    </div>
                </div>

                <!-- Quota Bar -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Quota</span>
                        <span class="text-xs text-gray-600 dark:text-gray-400">${voucher.remaining_quota || 0}</span>
                    </div>
                    <div class="quota-bar">
                        <div class="quota-bar-fill" style="width: ${percentQuota}%; background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);"></div>
                    </div>
                </div>
            </div>

            <!-- Action Button & Detail -->
            <div class="flex flex-col items-center justify-center pr-2 gap-1">
                <button 
                    type="button"
                    onclick="event.stopPropagation(); showVoucherModal('${voucher.id}')"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 p-1 transition-colors"
                    title="Detail">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
                
                <button 
                    type="button"
                    onclick="event.stopPropagation(); copyCheckoutVoucherCode('${voucher.code}')"
                    class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 p-1 transition-colors"
                    title="Copy Kode">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>
    `;
}

function isVoucherApplied(voucherId) {
    const search = document.querySelector('[id*="checkout-vouchers"]');
    return document.querySelector(`[onclick*="removeCheckoutVoucher('${voucherId}')"]`) !== null;
}

function selectVoucher(voucher) {
    if (voucher.can_apply === false) return;
    applyCheckoutVoucherDirect(voucher.code);
}

async function applyCheckoutVoucherDirect(code) {
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
            // Reload page untuk update
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showCheckoutError(result.message);
        }
    } catch (error) {
        showCheckoutError('Gagal menerapkan voucher!');
        console.error('Error:', error);
    }
}

function showVoucherModal(voucherId) {
    const voucher = allVouchers.find(v => v.id === voucherId);
    if (!voucher) return;

    currentModalVoucherId = voucherId;
    const modal = document.getElementById('voucherDetailModal');

    // Set header
    document.getElementById('modalTitle').textContent = voucher.name;

    // Set image
    const imageUrl = voucher.image_path ? `{{ asset('') }}${voucher.image_path}` : '/images/voucher-placeholder.png';
    document.getElementById('modalImage').innerHTML = `<img src="${imageUrl}" alt="${voucher.name}" class="w-full h-full object-cover">`;

    // Set description
    document.getElementById('modalDescription').textContent = voucher.description || 'Tidak ada deskripsi';

    // Set type & value
    document.getElementById('modalType').textContent = voucher.type_label;
    document.getElementById('modalValue').textContent = `Rp${voucher.value.toLocaleString('id-ID')}`;

    // Set terms
    const termsList = document.getElementById('modalTerms');
    termsList.innerHTML = '';
    const terms = [];
    if (voucher.minimum_purchase) terms.push(`Minimum: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}`);
    if (voucher.maximum_discount) terms.push(`Maksimum: Rp${voucher.maximum_discount.toLocaleString('id-ID')}`);
    if (voucher.remaining_quota) terms.push(`Sisa Quota: ${voucher.remaining_quota}`);
    terms.push(`Bisa dikombinasi: ${voucher.is_combinable ? 'Ya' : 'Tidak'}`);

    terms.forEach(term => {
        const li = document.createElement('li');
        li.textContent = term;
        termsList.appendChild(li);
    });

    // Set reasons if not applicable
    const reasonsSection = document.getElementById('modalReasons');
    if (voucher.can_apply === false && voucher.reasons) {
        reasonsSection.classList.remove('hidden');
        const reasonsList = document.getElementById('reasonsList');
        reasonsList.innerHTML = '';
        (Array.isArray(voucher.reasons) ? voucher.reasons : [voucher.reasons]).forEach(reason => {
            const li = document.createElement('li');
            li.textContent = reason;
            reasonsList.appendChild(li);
        });
    } else {
        reasonsSection.classList.add('hidden');
    }

    // Set button state
    const applyBtn = document.getElementById('modalApplyBtn');
    if (voucher.can_apply === false) {
        applyBtn.disabled = true;
        applyBtn.classList.add('button-disabled');
        applyBtn.textContent = 'Tidak Bisa Digunakan';
    } else {
        applyBtn.disabled = false;
        applyBtn.classList.remove('button-disabled');
        applyBtn.textContent = 'Terapkan';
    }

    modal.classList.remove('hidden');
}

function closeVoucherModal() {
    document.getElementById('voucherDetailModal').classList.add('hidden');
    currentModalVoucherId = null;
}

function applyVoucherFromModal() {
    const voucher = allVouchers.find(v => v.id === currentModalVoucherId);
    if (voucher && voucher.can_apply !== false) {
        closeVoucherModal();
        applyCheckoutVoucherDirect(voucher.code);
    }
}

function copyCheckoutVoucherCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const input = document.getElementById('checkout-voucher-search');
        input.value = code;
        input.focus();
        showCheckoutSuccess(`✓ Kode '${code}' disalin!`);
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

function searchCheckoutVouchers() {
    const query = document.getElementById('checkout-voucher-search').value.trim().toLowerCase();
    if (!query) {
        renderVoucherCards(allVouchers);
        return;
    }

    const filtered = allVouchers.filter(v => 
        v.name.toLowerCase().includes(query) || 
        v.code.toLowerCase().includes(query) ||
        v.description.toLowerCase().includes(query)
    );

    renderVoucherCards(filtered);
}

function showCheckoutSuccess(message) {
    // Create temporary notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-40';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function showCheckoutError(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-40';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Remove voucher function
async function removeCheckoutVoucher(voucherId) {
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
            showCheckoutSuccess('Voucher berhasil dihapus!');
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showCheckoutError(result.message);
        }
    } catch (error) {
        showCheckoutError('Gagal menghapus voucher!');
        console.error('Error:', error);
    }
}

// Close modal ketika click outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('voucherDetailModal');
    if (event.target === modal) {
        closeVoucherModal();
    }
});

// Enter key on search
document.getElementById('checkout-voucher-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') searchCheckoutVouchers();
});
</script>
