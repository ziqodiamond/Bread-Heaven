<!-- Voucher Section untuk Checkout - dengan Carousel -->
@props(['appliedVouchers' => null, 'subtotal' => 0])

<div id="voucher-container">
    <!-- Applied Vouchers Display -->
    @if ($appliedVouchers && count($appliedVouchers) > 0)
        <div class="mb-4 space-y-2">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">✓ Voucher Teraplikasi:</p>
            
            @foreach ($appliedVouchers as $voucher)
                <div class="flex items-center justify-between rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-700 dark:text-green-300">
                            {{ $voucher['name'] }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400">
                            Kode: <span class="font-mono">{{ $voucher['code'] }}</span>
                        </p>
                    </div>
                    <button 
                        type="button"
                        onclick="removeCheckoutVoucher('{{ $voucher['id'] }}')"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="mb-4 rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-700/50">
            <p class="text-sm text-gray-600 dark:text-gray-300">Belum ada voucher diterapkan</p>
        </div>
    @endif

    <!-- Available Vouchers Carousel -->
    <div class="mb-4 border-t pt-4 dark:border-gray-700">
        <p class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-300">
            🎁 Promo Tersedia:
        </p>
        
        <div class="relative">
            <div id="checkout-vouchers-carousel" class="flex gap-3 overflow-x-auto pb-2 snap-x scrollbar-hide">
                <!-- Loading -->
                <div id="checkout-vouchers-loading" class="text-center text-sm text-gray-500">Memuat voucher...</div>
            </div>
        </div>
    </div>

    <!-- Add Voucher Form -->
    <div class="space-y-3 border-t pt-4 dark:border-gray-700">
        <div class="flex gap-2">
            <input 
                type="text" 
                id="checkout-voucher-input"
                placeholder="Masukkan kode voucher atau salin dari promo di atas"
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
            <button 
                type="button"
                onclick="applyCheckoutVoucher()"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700">
                Terapkan
            </button>
        </div>
        <p id="checkout-voucher-error" class="text-xs text-red-600 dark:text-red-400 hidden"></p>
        <p id="checkout-voucher-success" class="text-xs text-green-600 dark:text-green-400 hidden"></p>
    </div>
</div>

<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
</style>

<script>
// Load available vouchers saat page load
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutAvailableVouchers();
});

async function loadCheckoutAvailableVouchers() {
    try {
        const response = await fetch('{{ route("cart.vouchers.available") }}');
        const result = await response.json();
        
        if (result.success && result.data) {
            const carousel = document.getElementById('checkout-vouchers-carousel');
            carousel.innerHTML = '';
            
            result.data.forEach(voucher => {
                const voucherCard = document.createElement('div');
                voucherCard.className = 'flex-shrink-0 w-72 snap-start';
                voucherCard.innerHTML = `
                    <div class="rounded-xl border border-gray-200 p-4 bg-gradient-to-br from-blue-50 to-white dark:border-gray-700 dark:bg-gray-700 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    ${voucher.name}
                                </h4>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                    ${voucher.description || ''}
                                </p>
                            </div>
                            ${voucher.is_sold_out ? '<span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-900 dark:text-red-200 whitespace-nowrap ml-2">Habis</span>' : ''}
                        </div>
                        
                        <div class="mt-3 space-y-2 border-t pt-3 dark:border-gray-600">
                            <div class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                ${voucher.type_label}: Rp${voucher.value.toLocaleString('id-ID')}
                                ${voucher.maximum_discount ? `<br><span class="text-xs text-gray-600 dark:text-gray-400">(Maks: Rp${voucher.maximum_discount.toLocaleString('id-ID')})</span>` : ''}
                            </div>
                            
                            ${voucher.minimum_purchase ? `<div class="text-xs text-gray-600 dark:text-gray-400">💰 Min: Rp${voucher.minimum_purchase.toLocaleString('id-ID')}</div>` : ''}
                            
                            ${voucher.remaining_quota ? `<div class="text-xs text-gray-600 dark:text-gray-400">📊 Sisa: ${voucher.remaining_quota}</div>` : ''}
                            
                            <button
                                type="button"
                                onclick="copyCheckoutVoucherCode('${voucher.code}')"
                                class="w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors"
                            >
                                📋 Salin Kode: ${voucher.code}
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

function copyCheckoutVoucherCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const input = document.getElementById('checkout-voucher-input');
        input.value = code;
        input.focus();
        
        // Tampilkan notif
        const notification = document.getElementById('checkout-voucher-success');
        notification.textContent = `✓ Kode '${code}' disalin ke input!`;
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

async function applyCheckoutVoucher() {
    const code = document.getElementById('checkout-voucher-input').value.trim();
    const errorEl = document.getElementById('checkout-voucher-error');
    const successEl = document.getElementById('checkout-voucher-success');
    
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');
    
    if (!code) {
        errorEl.textContent = '⚠️ Masukkan kode voucher terlebih dahulu!';
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
            successEl.textContent = '✓ ' + result.message;
            successEl.classList.remove('hidden');
            document.getElementById('checkout-voucher-input').value = '';
            
            // Reload page untuk update summary
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            errorEl.textContent = '✗ ' + result.message;
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = '✗ Terjadi kesalahan saat menerapkan voucher!';
        errorEl.classList.remove('hidden');
        console.error('Error:', error);
    }
}

async function removeCheckoutVoucher(voucherId) {
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
</script>
