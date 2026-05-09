{{-- resources/views/admin/management/product/partials/form-edit.blade.php --}}
{{--
    Tidak ada x-data di sini.
    Semua state diakses dari productManager() di root index.blade.php:
      img            → imgState[editId]
      visibleExisting → getter: existing yang belum dihapus
      hasNoImages    → getter: true jika tidak ada gambar sama sekali
--}}

<form action="{{ route('admin.management.products.update', $product) }}" method="POST" enctype="multipart/form-data"
    class="space-y-5">

    @csrf
    @method('PUT')

    {{-- ── Nama + Kategori ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
            <label for="edit_name_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Nama Produk <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="edit_name_{{ $product->id }}"
                value="{{ old('name', $product->name) }}" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="edit_category_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Kategori <span class="text-red-500">*</span>
            </label>
            <input type="text" name="category" id="edit_category_{{ $product->id }}"
                value="{{ old('category', $product->category) }}" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
    </div>

    {{-- ── Deskripsi ────────────────────────────────────────────────── --}}
    <div class="space-y-1.5">
        <label for="edit_description_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
            Deskripsi <span class="text-red-500">*</span>
        </label>
        <textarea name="description" id="edit_description_{{ $product->id }}" rows="3" required
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                   placeholder:text-gray-400 resize-none focus:border-gray-400 focus:outline-none focus:ring-0">{{ old('description', $product->description) }}</textarea>
    </div>

    {{-- ── Harga + Stok ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
            <label for="edit_price_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Harga <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                <input type="number" name="price" id="edit_price_{{ $product->id }}"
                    value="{{ old('price', $product->price) }}" min="0" required
                    class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="space-y-1.5">
            <label for="edit_stock_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Stok <span class="text-red-500">*</span>
            </label>
            <input type="number" name="stock" id="edit_stock_{{ $product->id }}"
                value="{{ old('stock', $product->stock) }}" min="0" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
    </div>

    {{-- ── Berat + Dimensi ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="space-y-1.5">
            <label for="edit_weight_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Berat (gram)
            </label>
            <input type="number" name="weight" id="edit_weight_{{ $product->id }}"
                value="{{ old('weight', $product->weight) }}" min="0" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="edit_length_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Panjang (cm)
            </label>
            <input type="number" name="length" id="edit_length_{{ $product->id }}"
                value="{{ old('length', $product->length) }}" min="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="edit_width_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Lebar (cm)
            </label>
            <input type="number" name="width" id="edit_width_{{ $product->id }}"
                value="{{ old('width', $product->width) }}" min="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="edit_height_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
                Tinggi (cm)
            </label>
            <input type="number" name="height" id="edit_height_{{ $product->id }}"
                value="{{ old('height', $product->height) }}" min="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
    </div>

    {{-- ── Status ───────────────────────────────────────────────────── --}}
    <div class="space-y-1.5">
        <label for="edit_status_{{ $product->id }}" class="block text-xs font-medium text-gray-600">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" id="edit_status_{{ $product->id }}" required
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                   focus:border-gray-400 focus:outline-none focus:ring-0">
            <option value="available" {{ old('status', $product->status) === 'available' ? 'selected' : '' }}>
                Available
            </option>
            <option value="not_available" {{ old('status', $product->status) === 'not_available' ? 'selected' : '' }}>
                Not Available
            </option>
        </select>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         FOTO PRODUK
    ══════════════════════════════════════════════════════════════ --}}
    <div class="space-y-2">

        {{-- Label + legend --}}
        <div class="flex items-center justify-between">
            <label class="block text-xs font-medium text-gray-600">Foto Produk</label>
            <div class="flex items-center gap-3 text-[10px] text-gray-400">
                <span class="flex items-center gap-1">
                    <span class="inline-block h-3 w-3 rounded-full bg-amber-400"></span>
                    Utama (tersimpan)
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block h-3 w-3 rounded-full bg-blue-400"></span>
                    Utama (baru)
                </span>
            </div>
        </div>

        {{-- Hint --}}
        <p class="text-[11px] text-gray-400">
            Klik foto untuk jadikan <span class="font-medium text-amber-500">foto utama</span>.
            Hover lalu klik <span class="text-red-400 font-medium">✕</span> untuk hapus.
        </p>

        {{-- ── Drop zone — muncul jika semua gambar sudah dihapus ─────── --}}
        <template x-if="hasNoImages">
            <label for="edit_images_{{ $product->id }}"
                class="flex flex-col items-center justify-center gap-2 rounded-2xl border border-dashed
                       border-gray-300 px-5 py-8 cursor-pointer hover:border-gray-400 hover:bg-gray-50
                       transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 group-hover:text-gray-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0
                           L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0
                           00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Klik untuk upload foto produk</p>
                    <p class="text-xs text-gray-400 mt-1">Bisa pilih lebih dari satu sekaligus</p>
                    <p class="text-[11px] text-gray-300 mt-1">PNG, JPG, WEBP — maks. 2MB per file</p>
                </div>
            </label>
        </template>

        {{-- ── Grid gambar — muncul jika ada gambar ───────────────────── --}}
        <template x-if="!hasNoImages">
            <div class="grid grid-cols-4 gap-2">

                {{-- Gambar EXISTING (yang sudah tersimpan di DB) --}}
                <template x-for="eximg in visibleExisting" :key="'ex-' + eximg.id">
                    <div @click="setPrimaryExisting(eximg.id)"
                        :class="img && img.primaryExistingId === eximg.id ?
                            'ring-2 ring-amber-400 border-amber-400' :
                            'border-gray-200 hover:border-gray-300'"
                        class="relative group rounded-xl overflow-hidden border aspect-square
                               bg-gray-50 cursor-pointer transition-all">

                        <img :src="eximg.url" :alt="eximg.alt" class="w-full h-full object-cover">

                        {{-- Badge Utama --}}
                        <template x-if="img && img.primaryExistingId === eximg.id">
                            <div
                                class="absolute top-1.5 left-1.5 flex items-center gap-1 rounded-full
                                        bg-amber-400 px-2 py-0.5 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12
                                             17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                                <span class="text-[9px] font-semibold text-white leading-none">Utama</span>
                            </div>
                        </template>

                        {{-- Badge Tersimpan --}}
                        <template x-if="!img || img.primaryExistingId !== eximg.id">
                            <div class="absolute top-1.5 left-1.5 rounded-full bg-black/30 px-1.5 py-0.5">
                                <span class="text-[9px] text-white leading-none">Tersimpan</span>
                            </div>
                        </template>

                        {{-- Tombol hapus --}}
                        <button type="button" @click.stop="removeExisting(eximg.id)"
                            class="absolute top-1.5 right-1.5 flex h-5 w-5 items-center justify-center
                                   rounded-full bg-black/50 opacity-0 group-hover:opacity-100
                                   transition-opacity hover:bg-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Gambar BARU (belum tersimpan, baru dipilih) --}}
                <template x-for="(nimg, nindex) in (img ? img.newImages : [])" :key="'new-' + nindex">
                    <div @click="setPrimaryNew(nindex)"
                        :class="img && img.primaryNewIndex === nindex ?
                            'ring-2 ring-blue-400 border-blue-400' :
                            'border-gray-200 hover:border-gray-300'"
                        class="relative group rounded-xl overflow-hidden border aspect-square
                               bg-gray-50 cursor-pointer transition-all">

                        <img :src="nimg.url" class="w-full h-full object-cover">

                        {{-- Badge Utama (biru) --}}
                        <template x-if="img && img.primaryNewIndex === nindex">
                            <div
                                class="absolute top-1.5 left-1.5 flex items-center gap-1 rounded-full
                                        bg-blue-400 px-2 py-0.5 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12
                                             17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                                <span class="text-[9px] font-semibold text-white leading-none">Utama</span>
                            </div>
                        </template>

                        {{-- Badge Baru --}}
                        <template x-if="!img || img.primaryNewIndex !== nindex">
                            <div class="absolute top-1.5 left-1.5 rounded-full bg-blue-500/70 px-1.5 py-0.5">
                                <span class="text-[9px] text-white leading-none">Baru</span>
                            </div>
                        </template>

                        {{-- Tombol hapus --}}
                        <button type="button" @click.stop="removeNew(nindex)"
                            class="absolute top-1.5 right-1.5 flex h-5 w-5 items-center justify-center
                                   rounded-full bg-black/50 opacity-0 group-hover:opacity-100
                                   transition-opacity hover:bg-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- Nama file --}}
                        <div class="absolute bottom-0 inset-x-0 px-1.5 py-1 bg-black/30">
                            <p class="text-[10px] text-white truncate" x-text="nimg.name"></p>
                        </div>
                    </div>
                </template>

                {{-- Tombol tambah foto --}}
                <label for="edit_images_{{ $product->id }}"
                    class="flex flex-col items-center justify-center gap-1 rounded-xl border border-dashed
                           border-gray-300 aspect-square cursor-pointer hover:border-gray-400 hover:bg-gray-50
                           transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300 group-hover:text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-[10px] text-gray-400">Tambah</span>
                </label>
            </div>
        </template>

        {{-- ── Hidden inputs — dikirim ke controller ───────────────────── --}}

        {{-- IDs gambar yang dihapus --}}
        <template x-if="img">
            <template x-for="did in img.deletedIds" :key="'del-' + did">
                <input type="hidden" name="deleted_images[]" :value="String(did)">
            </template>
        </template>

        {{--
            primary_image_id → ID existing image yang jadi primary.
            Kirim string kosong jika null agar validasi nullable|uuid tetap lolos
            (sudah dinormalisasi di controller).
        --}}
        <input type="hidden" name="primary_image_id" :value="img?.primaryExistingId ?? ''">

        {{--
            primary_new_image_index → index gambar baru yang jadi primary.
            -1 = tidak ada gambar baru yang dijadikan primary.
        --}}
        <input type="hidden" name="primary_new_image_index" :value="img ? img.primaryNewIndex : -1">

        {{-- File input — onChange ditangani handleEditFileChange() di productManager --}}
        <input type="file" name="images[]" id="edit_images_{{ $product->id }}" multiple accept="image/*"
            class="hidden" @change="handleEditFileChange($event)">

    </div>

    {{-- ── Actions ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-end gap-2 pt-3">
        <button type="button" @click="editOpen = false"
            class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                   hover:bg-gray-50 transition-colors">
            Batal
        </button>
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
