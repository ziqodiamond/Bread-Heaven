{{-- resources/views/admin/management/categories/edit.blade.php --}}
<x-layout-admin>
    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Edit Kategori</h2>
            <p class="text-sm text-gray-400 mt-0.5">Ubah informasi kategori produk</p>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6">

            <form action="{{ route('admin.management.categories.update', $category) }}" method="POST" class="space-y-5">

                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-medium text-gray-600">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="Contoh: Roti Tawar"
                        value="{{ old('name', $category->name) }}" required
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    @error('name')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug (Read-only) --}}
                <div class="space-y-1.5">
                    <label for="slug" class="block text-xs font-medium text-gray-600">
                        Slug (otomatis)
                    </label>
                    <input type="text" name="slug" id="slug" placeholder="slug-kategori"
                        value="{{ old('slug', $category->slug) }}" readonly
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0 bg-gray-50">
                </div>

                {{-- Deskripsi --}}
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3" placeholder="Deskripsi kategori..."
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 resize-none focus:border-gray-400 focus:outline-none focus:ring-0">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon --}}
                <div class="space-y-1.5">
                    <label for="icon" class="block text-xs font-medium text-gray-600">
                        Icon (optional)
                    </label>
                    <input type="text" name="icon" id="icon" placeholder="Contoh: 🍞 atau fa-bread"
                        value="{{ old('icon', $category->icon) }}"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    @error('icon')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sort Order --}}
                <div class="space-y-1.5">
                    <label for="sort_order" class="block text-xs font-medium text-gray-600">
                        Urutan Tampil
                    </label>
                    <input type="number" name="sort_order" id="sort_order" min="0" placeholder="0"
                        value="{{ old('sort_order', $category->sort_order) }}"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    @error('sort_order')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                        <span class="text-xs font-medium text-gray-600">Aktif</span>
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-5 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.management.categories.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>
</x-layout-admin>
