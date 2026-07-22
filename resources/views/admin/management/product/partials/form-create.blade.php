{{-- resources/views/admin/management/product/partials/form-create.blade.php --}}
{{-- Tidak ada x-data di sini — semua state dari productManager() --}}

<form action="{{ route('admin.management.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
    x-data="{ discountEnabled: false }"
    @submit="
        const checkbox = document.querySelector('input[name=enable_discount]');
        if (!checkbox.checked) {
            document.getElementById('create_discount_type').value = '';
            document.getElementById('create_discount_value').value = '';
            document.getElementById('create_discount_label').value = '';
            document.getElementById('create_discount_start').value = '';
            document.getElementById('create_discount_end').value = '';
            document.getElementById('create_sale_price').value = '';
        }
    ">

    @csrf

    {{-- ── Nama + SKU + Kategori ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-1.5">
            <label for="create_name" class="block text-xs font-medium text-gray-600">
                Nama Produk <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="create_name" placeholder="Contoh: Roti Coklat Premium" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="create_sku" class="block text-xs font-medium text-gray-600">
                SKU <span class="text-red-500">*</span>
            </label>
            <input type="text" name="sku" id="create_sku" placeholder="Contoh: RTI-CHOC-001" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="create_category" class="block text-xs font-medium text-gray-600">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select name="category_id" id="create_category" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── Deskripsi ────────────────────────────────────────────────── --}}
    <div class="space-y-1.5">
        <label for="create_description" class="block text-xs font-medium text-gray-600">
            Deskripsi <span class="text-red-500">*</span>
        </label>
        <textarea name="description" id="create_description" rows="4" placeholder="Tulis deskripsi produk..." required
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                   placeholder:text-gray-400 resize-none focus:border-gray-400 focus:outline-none focus:ring-0"></textarea>
    </div>

    {{-- ── Harga + Stok ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
            <label for="create_price" class="block text-xs font-medium text-gray-600">
                Harga <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                <input type="number" name="price" id="create_price" min="0" placeholder="0" required
                    class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-700
                          placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="space-y-1.5">
            <label for="create_stock" class="block text-xs font-medium text-gray-600">
                Stok <span class="text-red-500">*</span>
            </label>
            <input type="number" name="stock" id="create_stock" min="0" placeholder="0" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                      placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
    </div>

    {{-- ── Berat + Dimensi ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="space-y-1.5">
            <label for="create_weight" class="block text-xs font-medium text-gray-600">Berat (gram)</label>
            <input type="number" name="weight" id="create_weight" min="0" placeholder="0" required
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="create_length" class="block text-xs font-medium text-gray-600">Panjang (cm)</label>
            <input type="number" name="length" id="create_length" min="0" placeholder="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="create_width" class="block text-xs font-medium text-gray-600">Lebar (cm)</label>
            <input type="number" name="width" id="create_width" min="0" placeholder="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
        <div class="space-y-1.5">
            <label for="create_height" class="block text-xs font-medium text-gray-600">Tinggi (cm)</label>
            <input type="number" name="height" id="create_height" min="0" placeholder="0"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
        </div>
    </div>

    {{-- ── Status ───────────────────────────────────────────────────── --}}
    <div class="space-y-1.5">
        <label for="create_status" class="block text-xs font-medium text-gray-600">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" id="create_status" required
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                   focus:border-gray-400 focus:outline-none focus:ring-0">
            <option value="available">Available</option>
            <option value="not_available">Not Available</option>
        </select>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DISKON PRODUK - SIMPLE CALCULATOR
    ══════════════════════════════════════════════════════════════ --}}
    <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-medium text-gray-900">Diskon Produk</h3>
                <p class="text-xs text-gray-400 mt-0.5">Masukkan diskon atau harga jual</p>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="enable_discount" value="1"
                    @change="
                        discountEnabled = $el.checked;
                        if (!$el.checked) {
                            document.getElementById('create_discount_type').value = '';
                            document.getElementById('create_discount_value').value = '';
                            document.getElementById('create_discount_label').value = '';
                            document.getElementById('create_discount_start').value = '';
                            document.getElementById('create_discount_end').value = '';
                            document.getElementById('create_sale_price').value = '';
                        }
                    "
                    class="h-4 w-4 rounded border-gray-300">
                <span class="text-xs font-medium text-gray-600">Aktifkan</span>
            </label>
        </div>

        <div x-data="discountCalc({
            priceId: 'create_price',
            salePriceId: 'create_sale_price',
            discountTypeId: 'create_discount_type',
            discountValueId: 'create_discount_value',
            discountInfoId: 'create_discount_info',
            initialPrice: 0,
            initialSalePrice: null,
            initialType: '',
            initialValue: null
        })" x-show="discountEnabled"
            class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-3 bg-gray-50 rounded-xl">

            {{-- Tipe Diskon --}}
            <div class="space-y-1.5">
                <label for="create_discount_type" class="block text-xs font-medium text-gray-600">
                    Tipe Diskon
                </label>
                <select id="create_discount_type" name="discount_type" @change="updateType($event)"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                           focus:border-gray-400 focus:outline-none focus:ring-0">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="percent">Persen (%)</option>
                    <option value="fixed">Potongan Harga (Rp)</option>
                </select>
            </div>

            {{-- Nilai Diskon --}}
            <div class="space-y-1.5">
                <label for="create_discount_value" class="block text-xs font-medium text-gray-600">
                    Nilai Diskon <span class="text-gray-400"
                        x-text="discountType === 'percent' ? '(%)' : '(Rp)'"></span>
                </label>
                <input type="number" id="create_discount_value" name="discount_value"
                    @input="updateFromDiscount($event)" min="0" step="0.01"
                    placeholder="Input nilai diskon"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                           focus:border-gray-400 focus:outline-none focus:ring-0">
                <p class="text-xs text-gray-500 mt-1" x-show="msg" x-text="msg"></p>
            </div>

            {{-- Harga Jual --}}
            <div class="space-y-1.5 md:col-span-2">
                <label for="create_sale_price" class="block text-xs font-medium text-gray-600">
                    Harga Jual (Rp)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                    <input type="number" id="create_sale_price" name="sale_price"
                        @input="updateFromSalePrice($event)" min="0"
                        class="w-full rounded-xl border border-gray-200 pl-10 pr-3 py-2.5 text-sm text-gray-700
                               focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
            </div>

            {{-- Info Message --}}
            <div id="create_discount_info" class="md:col-span-2 text-xs text-gray-600 p-2 bg-blue-50 rounded-lg"
                x-show="infoMsg" x-text="infoMsg"></div>

            {{-- Label Diskon --}}
            <div class="space-y-1.5 md:col-span-2">
                <label for="create_discount_label" class="block text-xs font-medium text-gray-600">
                    Label Diskon (Promo, Flash Sale, dll)
                </label>
                <input type="text" name="discount_label" id="create_discount_label"
                    placeholder="Contoh: Promo Spesial"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                           focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </div>

        {{-- Jadwal Diskon --}}
        <div class="mt-3" x-show="discountEnabled">
            <p class="text-xs font-medium text-gray-600 mb-2">Jadwal Diskon (Opsional)</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="create_discount_start" class="block text-xs font-medium text-gray-600">
                        Mulai Diskon
                    </label>
                    <input type="datetime-local" name="discount_start_at" id="create_discount_start"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
                <div class="space-y-1.5">
                    <label for="create_discount_end" class="block text-xs font-medium text-gray-600">
                        Akhir Diskon
                    </label>
                    <input type="datetime-local" name="discount_end_at" id="create_discount_end"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         FOTO PRODUK
    ══════════════════════════════════════════════════════════════ --}}
    <div class="space-y-1.5">

        <div class="flex items-center justify-between">
            <label class="block text-xs font-medium text-gray-600">
                Foto Produk <span class="text-red-500">*</span>
            </label>
            <template x-if="createImages.length > 0">
                <div class="flex items-center gap-3 text-[10px] text-gray-400">
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-3 w-3 rounded-full bg-amber-400"></span>
                        Foto utama
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="inline-block h-3 w-3 rounded-full border border-gray-300 bg-white"></span>
                        Foto biasa
                    </span>
                </div>
            </template>
        </div>

        {{-- Drop zone — muncul jika belum ada foto --}}
        <template x-if="createImages.length === 0">
            <label for="create_images"
                class="flex flex-col items-center justify-center gap-2 rounded-2xl border border-dashed
                       border-gray-300 px-5 py-8 cursor-pointer hover:border-gray-400 hover:bg-gray-50
                       transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 group-hover:text-gray-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0
                           L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Klik untuk upload foto produk</p>
                    <p class="text-xs text-gray-400 mt-1">Bisa pilih lebih dari satu sekaligus</p>
                    <p class="text-[11px] text-gray-300 mt-1">PNG, JPG, WEBP — maks. 2MB per file</p>
                </div>
            </label>
        </template>

        {{-- Grid preview — muncul jika sudah ada foto --}}
        <template x-if="createImages.length > 0">
            <div>
                <p class="text-[11px] text-gray-400 mb-2">
                    Klik foto untuk jadikan <span class="font-medium text-amber-500">foto utama</span>.
                    Foto utama ditampilkan pertama di halaman produk.
                </p>

                <div class="grid grid-cols-4 gap-2">
                    <template x-for="(img, index) in createImages" :key="index">
                        <div @click="primaryIndex = index"
                            :class="primaryIndex === index ?
                                'ring-2 ring-amber-400 border-amber-400' :
                                'border-gray-200 hover:border-gray-300'"
                            class="relative group rounded-xl overflow-hidden border aspect-square
                                   bg-gray-50 cursor-pointer transition-all">

                            <img :src="img.url" class="w-full h-full object-cover">

                            {{-- Badge Utama --}}
                            <template x-if="primaryIndex === index">
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

                            {{-- Tombol hapus --}}
                            <button type="button" @click.stop="removeCreateImage(index)"
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
                                <p class="text-[10px] text-white truncate" x-text="img.name"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Tombol tambah foto lagi --}}
                    <label for="create_images"
                        class="flex flex-col items-center justify-center gap-1 rounded-xl border border-dashed
                               border-gray-300 aspect-square cursor-pointer hover:border-gray-400
                               hover:bg-gray-50 transition-all group">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-300 group-hover:text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-[10px] text-gray-400">Tambah</span>
                    </label>
                </div>

                {{-- primaryIndex dikirim ke controller --}}
                <input type="hidden" name="primary_image_index" :value="primaryIndex">
            </div>
        </template>

        {{--
            Satu input file — source of truth saat submit.
            Tidak pakai @change inline panjang; logic ada di productManager().
        --}}
        <input type="file" name="images[]" id="create_images" multiple accept="image/*" class="hidden"
            @change="
                Array.from($event.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        createImages.push({ name: file.name, url: e.target.result, file });
                        syncCreateFiles();
                    };
                    reader.readAsDataURL(file);
                });
                $event.target.value = '';
            ">
    </div>

    {{-- ── Actions ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-end gap-2 pt-3">
        <button type="button" @click="createOpen = false"
            class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                   hover:bg-gray-50 transition-colors">
            Batal
        </button>
        <button type="submit"
            class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm
                   font-medium text-white hover:bg-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
            </svg>
            Simpan Produk
        </button>
    </div>
</form>
