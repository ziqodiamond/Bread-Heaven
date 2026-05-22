{{-- resources/views/admin/management/flash_sales/create.blade.php --}}
<x-layout-admin>
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.management.flash_sales.index') }}"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-base font-medium text-gray-900">Buat Flash Sale Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">Atur detail flash sale dan produk yang akan dijual</p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.management.flash_sales.store') }}" method="POST"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf

            {{-- Info Dasar --}}
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-medium text-gray-600">
                            Nama Flash Sale <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Flash Sale Akhir Bulan"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('name')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="label" class="block text-xs font-medium text-gray-600">
                            Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" id="label" value="{{ old('label') }}" required
                            placeholder="Contoh: Super Flash"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('label')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                        placeholder="Deskripsi flash sale ini..."
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0 resize-none">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Jadwal Flash Sale</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="start_at" class="block text-xs font-medium text-gray-600">
                            Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at') }}" required
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('start_at')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="end_at" class="block text-xs font-medium text-gray-600">
                            Berakhir <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at') }}" required
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('end_at')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Pengaturan Tampilan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan Tampilan</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Aktifkan flash sale</span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_countdown" value="1" {{ old('show_countdown') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Tampilkan countdown</span>
                    </label>
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="show_in_homepage" value="1" {{ old('show_in_homepage') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Tampilkan di homepage</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="badge_color" class="block text-xs font-medium text-gray-600">
                            Warna Badge
                        </label>
                        <input type="color" name="badge_color" id="badge_color" value="{{ old('badge_color', '#ef4444') }}"
                            class="w-full rounded-xl border border-gray-200 h-10 cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                        <label for="sort_order" class="block text-xs font-medium text-gray-600">
                            Urutan Tampil
                        </label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">SEO (Opsional)</h3>
                
                <div class="space-y-1.5">
                    <label for="meta_title" class="block text-xs font-medium text-gray-600">
                        Meta Title
                    </label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                        placeholder="Judul untuk search engine"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>

                <div class="space-y-1.5">
                    <label for="meta_description" class="block text-xs font-medium text-gray-600">
                        Meta Description
                    </label>
                    <textarea name="meta_description" id="meta_description" rows="2"
                        placeholder="Deskripsi untuk search engine"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0 resize-none">{{ old('meta_description') }}</textarea>
                </div>
            </div>

            {{-- Info Note --}}
            <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs font-medium text-blue-900">💡 Tips:</p>
                <ul class="text-xs text-blue-700 mt-1 space-y-0.5">
                    <li>• Setelah membuat flash sale, Anda bisa menambahkan produk di halaman edit</li>
                    <li>• Status flash sale akan otomatis berubah sesuai tanggal mulai dan berakhir</li>
                    <li>• Setiap produk di flash sale bisa punya harga berbeda</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.management.flash_sales.index') }}"
                    class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                           hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm
                           font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Buat Flash Sale
                </button>
            </div>

        </form>

    </div>
</x-layout-admin>
