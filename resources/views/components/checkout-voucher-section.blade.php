<!-- Voucher Section untuk Checkout -->
@props(['appliedVouchers' => null, 'subtotal' => 0])

<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            💳 Voucher & Promo
        </h3>
        <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-600">
            Maksimal 2 Voucher
        </span>
    </div>

    <!-- Applied Vouchers Display -->
    @if ($appliedVouchers && count($appliedVouchers) > 0)
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
                        onclick="removeCheckoutVoucher('{{ $voucher['id'] }}')"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach

            <div id="checkout-discount-display" class="mt-3 space-y-1 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                <!-- Discount summary will be inserted here by JavaScript -->
            </div>
        </div>
    @else
        <div class="mb-4 rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-700/50">
            <p class="text-sm text-gray-600 dark:text-gray-300">Belum ada voucher diterapkan</p>
        </div>
    @endif

    <!-- Add Voucher Form -->
    <div class="space-y-3 border-t pt-4 dark:border-gray-700">
        <div class="flex gap-2">
            <input 
                type="text" 
                id="checkout-voucher-input"
                placeholder="Masukkan kode voucher"
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

    <!-- Available Vouchers Info -->
    <div class="border-t pt-4 mt-4 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            💡 Tip: Kembali ke cart untuk melihat voucher tersedia, atau gunakan tombol "Salin" untuk copy kodenya langsung.
        </p>
    </div>
</div>

<script>
async function applyCheckoutVoucher() {
    const code = document.getElementById('checkout-voucher-input').value.trim();
    const errorEl = document.getElementById('checkout-voucher-error');
    const successEl = document.getElementById('checkout-voucher-success');
    
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
            document.getElementById('checkout-voucher-input').value = '';
            
            // Reload page untuk update summary
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
