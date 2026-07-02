{{-- resources/views/admin/management/flash_sales/edit.blade.php --}}
<x-layout-admin>
    <div class="max-w-4xl mx-auto space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.management.flash_sales.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-base font-medium text-gray-900">Edit Flash Sale</h2>
                    <p class="text-sm text-gray-400 mt-0.5">{{ $flashSale->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-gray-600">Status: <span class="text-blue-600">{{ $flashSale->status }}</span></p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $flashSale->items->count() }} produk aktif</p>
            </div>
        </div>

        {{-- ── Flash Sale Info ──────────────────────────────────────── --}}
        <form action="{{ route('admin.management.flash_sales.update', $flashSale) }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf
            @method('PUT')

            {{-- Info Dasar --}}
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-medium text-gray-600">
                            Nama Flash Sale
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $flashSale->name) }}" required
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>

                    <div class="space-y-1.5">
                        <label for="label" class="block text-xs font-medium text-gray-600">
                            Label
                        </label>
                        <input type="text" name="label" id="label" value="{{ old('label', $flashSale->label) }}" required
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               focus:border-gray-400 focus:outline-none focus:ring-0 resize-none">{{ old('description', $flashSale->description) }}</textarea>
                </div>
            </div>

            {{-- Banner & Thumbnail --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Banner & Thumbnail</h3>
                
                @if($flashSale->banner)
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-600">Banner Saat Ini:</p>
                        <img src="{{ $flashSale->banner_url }}" alt="Banner" class="h-32 w-full object-cover rounded-lg border border-gray-200">
                    </div>
                @endif
                
                <div class="space-y-1.5">
                    <label for="banner" class="block text-xs font-medium text-gray-600">
                        Ubah Banner (Gambar)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="banner" id="banner" accept="image/*"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    <p class="text-xs text-gray-400">Maksimal 5MB, format: JPG, PNG, GIF</p>
                </div>

                @if($flashSale->thumbnail)
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-600">Thumbnail Saat Ini:</p>
                        <img src="{{ $flashSale->thumbnail_url }}" alt="Thumbnail" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label for="thumbnail" class="block text-xs font-medium text-gray-600">
                        Ubah Thumbnail (Gambar)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    <p class="text-xs text-gray-400">Maksimal 5MB, format: JPG, PNG, GIF</p>
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Jadwal Flash Sale</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="start_at" class="block text-xs font-medium text-gray-600">
                            Mulai
                        </label>
                        <div class="relative">
                            <input type="text" name="start_at" id="start_at" data-input 
                                value="{{ old('start_at', $flashSale->start_at->format('Y-m-d H:i')) }}" required
                                placeholder="Pilih tanggal dan jam mulai"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" 
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="end_at" class="block text-xs font-medium text-gray-600">
                            Berakhir
                        </label>
                        <div class="relative">
                            <input type="text" name="end_at" id="end_at" data-input 
                                value="{{ old('end_at', $flashSale->end_at->format('Y-m-d H:i')) }}" required
                                placeholder="Pilih tanggal dan jam berakhir"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" 
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs text-blue-700">
                        💡 Klik pada field untuk membuka kalender. Anda bisa memilih tanggal dan jam dengan mudah.
                    </p>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $flashSale->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Aktif</span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_countdown" value="1" {{ old('show_countdown', $flashSale->show_countdown) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Tampilkan countdown</span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_in_homepage" value="1" {{ old('show_in_homepage', $flashSale->show_in_homepage) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Tampilkan di homepage</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="badge_color" class="block text-xs font-medium text-gray-600">
                            Warna Badge
                        </label>
                        <input type="color" name="badge_color" id="badge_color" 
                            value="{{ old('badge_color', $flashSale->badge_color ?? '#ef4444') }}"
                            class="w-full rounded-xl border border-gray-200 h-10 cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                        <label for="sort_order" class="block text-xs font-medium text-gray-600">
                            Urutan Tampil
                        </label>
                        <input type="number" name="sort_order" id="sort_order" 
                            value="{{ old('sort_order', $flashSale->sort_order) }}" min="0"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.management.flash_sales.index') }}"
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

        </form>

        {{-- ── Produk dalam Flash Sale ──────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white p-5 space-y-4">

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">Produk Flash Sale</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $items->count() }} produk</p>
                </div>
                <button type="button" onclick="toggleAddItemForm()" 
                    class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2
                           text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </button>
            </div>

            {{-- Add Item Form --}}
            <div id="addItemForm" class="hidden px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 space-y-3">
                <form action="{{ route('admin.management.flash_sales.addItem', $flashSale) }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="space-y-1.5">
                            <label for="product_id" class="block text-xs font-medium text-gray-600">
                                Produk <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:border-gray-400">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="flash_sale_price" class="block text-xs font-medium text-gray-600">
                                Harga Flash Sale <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="flash_sale_price" id="flash_sale_price" value="{{ old('flash_sale_price') }}" required min="0"
                                placeholder="Harga spesial"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 placeholder:text-gray-400
                                       focus:outline-none focus:border-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="stock_limit" class="block text-xs font-medium text-gray-600">
                                Stok Limit <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock_limit" id="stock_limit" value="{{ old('stock_limit') }}" required min="1"
                                placeholder="Jumlah stok"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 placeholder:text-gray-400
                                       focus:outline-none focus:border-gray-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="max_purchase_per_user" class="block text-xs font-medium text-gray-600">
                                Max Pembelian/User
                            </label>
                            <input type="number" name="max_purchase_per_user" id="max_purchase_per_user" 
                                value="{{ old('max_purchase_per_user', 10) }}" min="1"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                                       focus:outline-none focus:border-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="sort_order_item" class="block text-xs font-medium text-gray-600">
                                Urutan
                            </label>
                            <input type="number" name="sort_order" id="sort_order_item" 
                                value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                                       focus:outline-none focus:border-gray-400">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="toggleAddItemForm()"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600
                                   hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white
                                   hover:bg-gray-700 transition-colors">
                            Tambah Item
                        </button>
                    </div>
                </form>
            </div>

            {{-- Items List --}}
            <div class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->thumbnail }}" alt="{{ $item->product_name }}" 
                                class="h-10 w-10 rounded object-cover">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                    <span>Harga: Rp {{ number_format($item->sale_price, 0, ',', '.') }}</span>
                                    <span>•</span>
                                    <span>Stok: {{ $item->remaining_stock }} / {{ $item->stock_limit }}</span>
                                    <span>•</span>
                                    <span class="text-red-600 font-medium">-{{ $item->discount_percentage }}%</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.management.flash_sales.removeItem', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors"
                                onclick="return confirm('Hapus produk ini dari flash sale?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500">
                        <p class="text-sm">Belum ada produk dalam flash sale ini</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

    <script>
        function toggleAddItemForm() {
            const form = document.getElementById('addItemForm');
            form.classList.toggle('hidden');
        }
    </script>
</x-layout-admin>
