<!-- Voucher Section Component -->
@props(['appliedVouchers' => null, 'cartSummary' => null])

<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            💳 Voucher & Promo
        </h3>
        <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-600">
            Maksimal 2 Voucher
        </span>
    </div>

    <div id="voucher-container">
        <!-- Applied Vouchers -->
        @if ($appliedVouchers && $appliedVouchers->count() > 0)
            <div class="mb-4 space-y-2">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Voucher Teraplikasi:</p>
                
                @foreach ($appliedVouchers as $voucher)
                    <div class="flex items-center justify-between rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-green-700 dark:text-green-300">
                                ✓ {{ $voucher['name'] }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400">
                                Kode: <span class="font-mono">{{ $voucher['code'] }}</span>
                            </p>
                        </div>
                        <button 
                            type="button"
                            onclick="removeVoucher('{{ $voucher['id'] }}')"
                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Voucher Input -->
        <div class="space-y-3 mb-4">
            <div class="flex gap-2">
                <input 
                    type="text" 
                    id="voucher-input"
                    placeholder="Masukkan kode voucher"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                <button 
                    type="button"
                    onclick="applyVoucher()"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Terapkan
                </button>
            </div>
            <p id="voucher-error" class="text-xs text-red-600 dark:text-red-400 hidden"></p>
            <p id="voucher-success" class="text-xs text-green-600 dark:text-green-400 hidden"></p>
        </div>

        <!-- Available Vouchers Carousel -->
        <div class="border-t pt-4 dark:border-gray-700">
            <p class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                Promo Tersedia:
            </p>
            
            <div class="relative">
                <div id="vouchers-carousel" class="flex gap-3 overflow-x-auto pb-2 snap-x">
                    <!-- Loading -->
                    <div id="vouchers-loading" class="text-center text-sm text-gray-500">Memuat...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Summary -->
    @if ($cartSummary)
        <div class="border-t pt-4 mt-4 dark:border-gray-700 space-y-2">
            @if ($cartSummary['total_discount'] > 0)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Potongan Diskon:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                        -Rp{{ number_format($cartSummary['total_discount'], 0, ',', '.') }}
                    </span>
                </div>
            @endif

            @if ($cartSummary['total_shipping_discount'] > 0)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Gratis Ongkir:</span>
                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                        -Rp{{ number_format($cartSummary['total_shipping_discount'], 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>
    @endif
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
