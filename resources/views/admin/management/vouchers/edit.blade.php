{{-- resources/views/admin/management/vouchers/edit.blade.php --}}
<x-layout-admin>
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.management.vouchers.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-base font-medium text-gray-900">Edit Voucher</h2>
                    <p class="text-sm text-gray-400 mt-0.5">{{ $voucher->code }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-gray-600">Status: 
                    @if ($voucher->is_active && !$voucher->expired_at?->isPast())
                        <span class="text-green-600">Active</span>
                    @elseif ($voucher->expired_at?->isPast())
                        <span class="text-gray-600">Expired</span>
                    @else
                        <span class="text-yellow-600">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.management.vouchers.update', $voucher) }}" method="POST"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf
            @method('PUT')

            {{-- Kode & Deskripsi --}}
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Informasi Dasar</h3>
                
                <div class="space-y-1.5">
                    <label for="code" class="block text-xs font-medium text-gray-600">
                        Kode Voucher
                    </label>
                    <input type="text" value="{{ $voucher->code }}" disabled
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 font-mono uppercase bg-gray-50">
                    <p class="text-xs text-gray-400">Kode tidak dapat diubah setelah dibuat</p>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">
                        Deskripsi (Opsional)
                    </label>
                    <input type="text" name="description" id="description" value="{{ old('description', $voucher->description) }}"
                        placeholder="Contoh: Flash Sale Akhir Bulan"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
            </div>

            {{-- Tipe & Nilai --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Tipe & Nilai Diskon</h3>
                
                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-medium text-gray-600">
                        Tipe
                    </label>
                    <input type="text" value="{{ ucfirst($voucher->type) }}" disabled
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-gray-50">
                    <p class="text-xs text-gray-400">Tipe tidak dapat diubah</p>
                </div>

                @if ($voucher->type !== 'free_shipping')
                    <div class="space-y-1.5">
                        <label for="value" class="block text-xs font-medium text-gray-600">
                            @if ($voucher->type === 'percent')
                                Diskon (%)
                            @else
                                Diskon (Rp)
                            @endif
                        </label>
                        <div class="relative">
                            <input type="number" name="value" id="value" value="{{ old('value', $voucher->value) }}" 
                                placeholder="0" min="0" step="0.01"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">
                                @if ($voucher->type === 'percent')
                                    %
                                @endif
                            </span>
                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-xs text-blue-900">✓ Voucher ini memberikan gratis ongkir</p>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label for="min_purchase" class="block text-xs font-medium text-gray-600">
                        Pembelian Minimum (Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="min_purchase" id="min_purchase" value="{{ old('min_purchase', $voucher->min_purchase) }}" 
                            placeholder="0" min="0"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pl-8 text-sm text-gray-700
                                   placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- Kuota & Tanggal --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Kuota & Tanggal Kadaluarsa</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="quota" class="block text-xs font-medium text-gray-600">
                            Kuota
                        </label>
                        <div class="space-y-2">
                            <input type="number" name="quota" id="quota" value="{{ old('quota', $voucher->quota) }}" min="1"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       focus:border-gray-400 focus:outline-none focus:ring-0">
                            <div class="text-xs text-gray-500">
                                <p>Digunakan: <strong>{{ $voucher->usages->count() }}</strong> dari {{ $voucher->quota }}</p>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-1">
                                    <div class="h-full bg-gray-700 rounded-full" style="width: {{ ($voucher->usages->count() / $voucher->quota) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="expired_at" class="block text-xs font-medium text-gray-600">
                            Kadaluarsa Pada
                        </label>
                        <input type="datetime-local" name="expired_at" id="expired_at" 
                            value="{{ old('expired_at', $voucher->expired_at->format('Y-m-d\TH:i')) }}"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                        @if ($voucher->expired_at->isPast())
                            <p class="text-xs text-red-600 font-medium">Sudah Kadaluarsa</p>
                        @else
                            <p class="text-xs text-gray-500">{{ $voucher->expired_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Aktifkan voucher</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="allow_multiple_use" value="1" {{ old('allow_multiple_use', $voucher->allow_multiple_use) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Izinkan penggunaan berulang per pelanggan</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="apply_to_all" value="1" {{ old('apply_to_all', $voucher->apply_to_all) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Berlaku untuk semua produk</span>
                    </label>
                </div>
            </div>

            {{-- Statistik Penggunaan --}}
            @if ($voucher->usages->count() > 0)
                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Statistik Penggunaan</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="px-3 py-2.5 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Total Digunakan</p>
                            <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $voucher->usages->count() }}</p>
                        </div>
                        <div class="px-3 py-2.5 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Sisa Kuota</p>
                            <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $voucher->quota - $voucher->usages->count() }}</p>
                        </div>
                        <div class="px-3 py-2.5 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Persentase Penggunaan</p>
                            <p class="text-lg font-bold text-gray-900 mt-0.5">{{ round(($voucher->usages->count() / $voucher->quota) * 100) }}%</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <form action="{{ route('admin.management.vouchers.destroy', $voucher) }}" method="POST" class="inline"
                    onsubmit="return confirm('Hapus voucher ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700 transition-colors">
                        Hapus Voucher
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.management.vouchers.index') }}"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                               hover:bg-gray-50 transition-colors">
                        Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm
                               font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>

    </div>
</x-layout-admin>
