{{-- resources/views/admin/management/discounts/edit.blade.php --}}
<x-layout-admin>
    <div class="max-w-2xl mx-auto space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.management.discounts.index') }}"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-base font-medium text-gray-900">Edit Diskon Produk</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $product->name }}</p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.management.discounts.update', $product) }}" method="POST"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf
            @method('PUT')

            {{-- Info Produk --}}
            <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                    class="h-16 w-16 rounded-lg object-cover">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400">{{ $product->sku }}</p>
                    <p class="text-xs text-gray-500 mt-1">Harga Normal: <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span></p>
                </div>
            </div>

            {{-- Harga Diskon --}}
            <div class="space-y-1.5">
                <label for="sale_price" class="block text-xs font-medium text-gray-600">
                    Harga Diskon <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal">(harus lebih kecil dari harga normal)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                    <input type="number" name="sale_price" id="sale_price"
                        value="{{ old('sale_price', $product->sale_price) }}"
                        min="0" max="{{ $product->price - 1 }}" required
                        class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
                @error('sale_price')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe & Nilai Diskon --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="discount_type" class="block text-xs font-medium text-gray-600">
                        Tipe Diskon
                    </label>
                    <select name="discount_type" id="discount_type"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               focus:border-gray-400 focus:outline-none focus:ring-0">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>
                            Persen (%)
                        </option>
                        <option value="fixed" {{ old('discount_type', $product->discount_type) === 'fixed' ? 'selected' : '' }}>
                            Potongan Harga (Rp)
                        </option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="discount_value" class="block text-xs font-medium text-gray-600">
                        Nilai Diskon
                    </label>
                    <input type="number" name="discount_value" id="discount_value"
                        value="{{ old('discount_value', $product->discount_value) }}"
                        min="0" step="0.01"
                        placeholder="Contoh: 10 atau 5000"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
                <div class="space-y-1.5">
                    <label for="discount_max" class="block text-xs font-medium text-gray-600">
                        Maksimal Diskon (Persen) <span class="text-gray-400 font-normal">(opsional, hanya berlaku untuk tipe Persen)</span>
                    </label>
                    <input type="number" name="discount_max" id="discount_max"
                        value="{{ old('discount_max', $product->discount_max) }}"
                        min="0" max="100" step="1"
                        placeholder="Contoh: 50"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    @error('discount_max')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Label Diskon --}}
            <div class="space-y-1.5">
                <label for="discount_label" class="block text-xs font-medium text-gray-600">
                    Label Diskon <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <input type="text" name="discount_label" id="discount_label"
                    value="{{ old('discount_label', $product->discount_label) }}"
                    placeholder="Contoh: Promo Spesial, Flash Sale, Diskon Member"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>

            {{-- Jadwal Diskon --}}
            <div class="space-y-3">
                <p class="text-xs font-medium text-gray-600">Jadwal Diskon <span class="text-gray-400 font-normal">(opsional)</span></p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-3 bg-gray-50 rounded-xl">
                    <div class="space-y-1.5">
                        <label for="discount_start_at" class="block text-xs font-medium text-gray-600">
                            Mulai Diskon
                        </label>
                        <input type="datetime-local" name="discount_start_at" id="discount_start_at"
                            value="{{ old('discount_start_at', $product->discount_start_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    <div class="space-y-1.5">
                        <label for="discount_end_at" class="block text-xs font-medium text-gray-600">
                            Akhir Diskon
                        </label>
                        <input type="datetime-local" name="discount_end_at" id="discount_end_at"
                            value="{{ old('discount_end_at', $product->discount_end_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- Preview --}}
            <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs font-medium text-blue-900 mb-2">Preview Diskon:</p>
                <div class="text-sm text-blue-700">
                    <p>Harga Normal: <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span></p>
                    <p>Harga Diskon: <span class="font-medium text-red-600">Rp {{ number_format(old('sale_price', $product->sale_price) ?? 0, 0, ',', '.') }}</span></p>
                    @if (old('sale_price', $product->sale_price))
                        <p>Potongan: <span class="font-medium">Rp {{ number_format($product->price - (old('sale_price', $product->sale_price) ?? 0), 0, ',', '.') }}</span></p>
                        <p>Persentase: <span class="font-medium">{{ round((($product->price - (old('sale_price', $product->sale_price) ?? 0)) / $product->price) * 100, 0) }}%</span></p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.management.discounts.index') }}"
                    class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                           hover:bg-gray-50 transition-colors">
                    Kembali
                </a>
                <div class="flex gap-2">
                    <form action="{{ route('admin.management.discounts.destroy', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Hapus semua diskon untuk produk ini?')"
                            class="rounded-xl border border-red-200 px-4 py-2 text-sm text-red-600
                                   hover:bg-red-50 transition-colors">
                            Hapus Diskon
                        </button>
                    </form>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm
                               font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Diskon
                    </button>
                </div>
            </div>

        </form>

    </div>
</x-layout-admin>
