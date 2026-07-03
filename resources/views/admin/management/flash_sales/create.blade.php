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
        <form action="{{ route('admin.management.flash_sales.store') }}" method="POST" enctype="multipart/form-data"
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

            {{-- Banner & Thumbnail --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Banner & Thumbnail</h3>
                
                <div class="space-y-1.5">
                    <label for="banner" class="block text-xs font-medium text-gray-600">
                        Banner (Gambar)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="banner" id="banner" accept="image/*"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    <p class="text-xs text-gray-400">Maksimal 5MB, format: JPG, PNG, GIF</p>
                    @error('banner')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="thumbnail" class="block text-xs font-medium text-gray-600">
                        Thumbnail (Gambar)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    <p class="text-xs text-gray-400">Maksimal 5MB, format: JPG, PNG, GIF</p>
                    @error('thumbnail')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
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
                        <div class="relative">
                            <input type="text" name="start_at" id="start_at" data-input 
                                value="{{ old('start_at') }}" required
                                placeholder="Pilih tanggal dan jam mulai"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" 
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @error('start_at')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="end_at" class="block text-xs font-medium text-gray-600">
                            Berakhir <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="end_at" id="end_at" data-input 
                                value="{{ old('end_at') }}" required
                                placeholder="Pilih tanggal dan jam berakhir"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" 
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @error('end_at')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-4 py-2 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs text-blue-700">
                        💡 Klik pada field untuk membuka kalender. Anda bisa memilih tanggal dan jam dengan mudah.
                    </p>
                </div>
            </div>

            {{-- Pengaturan Tampilan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan Tampilan</h3>
                
                <div class="space-y-4">
                    <!-- Aktif Toggle -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-sm text-gray-600">Aktifkan flash sale</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:border after:border-gray-300 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <!-- Tampilkan Countdown Toggle -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-sm text-gray-600">Tampilkan countdown</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="show_countdown" value="0">
                            <input type="checkbox" name="show_countdown" value="1" {{ old('show_countdown') ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:border after:border-gray-300 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <!-- Tampilkan di Homepage Toggle -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-sm text-gray-600">Tampilkan di homepage</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="show_in_homepage" value="0">
                            <input type="checkbox" name="show_in_homepage" value="1" {{ old('show_in_homepage') ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:border after:border-gray-300 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
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
