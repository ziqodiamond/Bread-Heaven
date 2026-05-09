{{-- resources/views/admin/management/product/index.blade.php --}}
<x-layout-admin>
    {{--
        SATU x-data di root — productManager() kelola semua state.
        Modal Create, Edit, Delete semuanya di luar @foreach agar tidak
        di-render N kali dan state tidak tumpang tindih.
    --}}
    <div class="space-y-5" x-data="productManager()">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Product Management</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua produk yang tersedia</p>
        </div>



        {{-- ── Tabel ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar Produk</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $products->count() }} produk terdaftar</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                        </svg>
                        <input type="text" name="name" value="{{ request('name') }}"
                            placeholder="Cari nama produk..."
                            class="block w-full rounded-lg border border-gray-200 py-2 pl-8 pr-3 text-sm
                                   text-gray-700 placeholder-gray-400 focus:border-gray-400
                                   focus:outline-none focus:ring-0">
                    </div>
                    <button @click="openCreate()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2
                               text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Produk
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Produk</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Deskripsi</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Harga</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Stok</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Foto</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">

                                <td class="px-5 py-3.5 font-medium text-gray-900">
                                    {{ $product->name }}
                                    <p class="text-[11px] text-gray-400 font-normal mt-0.5">{{ $product->category }}</p>
                                </td>

                                <td class="px-5 py-3.5 text-gray-500 max-w-[200px]">
                                    <span class="block truncate">{{ $product->description }}</span>
                                </td>

                                <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $product->stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-700' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>

                                {{--
                                    Pakai accessor $product->thumbnail dari model Product.
                                    Sudah handle fallback ke no-image.png jika tidak ada primary image.
                                --}}
                                <td class="px-5 py-3.5">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                                        class="h-9 w-9 rounded-lg object-cover border border-gray-100">
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">

                                        {{-- Tombol Edit — kirim UUID sebagai string --}}
                                        <button @click="openEdit('{{ $product->id }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100
                                                   px-2.5 py-1.5 text-xs font-medium text-blue-700
                                                   hover:bg-blue-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                       m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>

                                        {{-- Tombol Delete — hanya set deleteId + buka modal --}}
                                        <button
                                            @click="openDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-100
                                                   px-2.5 py-1.5 text-xs font-medium text-red-600
                                                   hover:bg-red-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                                       L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                    Belum ada produk. Klik <strong>Tambah Produk</strong> untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════════
             MODAL EDIT
             Di luar @foreach — render sekali, konten berubah via x-show
        ══════════════════════════════════════════════════════════════ --}}
        <div x-show="editOpen" x-transition style="display:none"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">

            <div @click.outside="editOpen = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-sm overflow-y-auto max-h-[90vh]">

                <div class="flex items-center justify-between mb-5">
                    <p class="text-sm font-medium text-gray-900">Edit Produk</p>
                    <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{--
                    Render semua form sekaligus — toggle via x-show.
                    editId adalah string UUID, jadi pakai quote di comparison.
                --}}
                @foreach ($products as $product)
                    <div x-show="editId === '{{ $product->id }}'" style="display:none">
                        @include('admin.management.product.partials.form-edit', ['product' => $product])
                    </div>
                @endforeach

            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════════
             MODAL CREATE
        ══════════════════════════════════════════════════════════════ --}}
        <div x-show="createOpen" x-transition style="display:none"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">

            <div @click.outside="createOpen = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-3xl rounded-xl bg-white p-6 shadow-sm overflow-y-auto max-h-[90vh]">

                <div class="flex items-center justify-between mb-5">
                    <p class="text-sm font-medium text-gray-900">Tambah Produk</p>
                    <button @click="createOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @include('admin.management.product.partials.form-create')
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════════
             MODAL DELETE
             SATU modal — di luar loop. deleteId & deleteName di-set saat
             tombol delete diklik. Form action dinamis via deleteRoute.
        ══════════════════════════════════════════════════════════════ --}}
        <div x-show="deleteOpen" x-transition style="display:none"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">

            <div @click.outside="deleteOpen = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-sm rounded-xl bg-white p-6 shadow-sm">

                <div class="flex items-start gap-3 mb-5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Hapus Produk</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Produk <strong x-text="deleteName"></strong> akan dihapus secara permanen
                            beserta semua foto-nya. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                {{--
                    Form action di-build dari base URL + deleteId.
                    Kita pakai JS untuk set action sebelum submit.
                --}}
                <form method="POST" :action="deleteRoute" @submit.prevent="submitDelete">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="deleteOpen = false"
                            class="rounded-lg border border-gray-200 px-3.5 py-2 text-sm text-gray-600
                                   hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-red-600 px-3.5 py-2 text-sm font-medium text-white
                                   hover:bg-red-700 transition-colors">
                            Hapus
                        </button>
                    </div>
                </form>

            </div>
        </div>


    </div>{{-- end x-data --}}


    <script>
        function productManager() {
            return {

                /*
                |──────────────────────────────────────────────────────────
                | State modal
                |──────────────────────────────────────────────────────────
                */
                editOpen: false,
                createOpen: false,
                deleteOpen: false,
                editId: null,
                deleteId: null,
                deleteName: '',

                // Base URL route destroy — diganti productId saat openDelete()
                deleteRouteBase: '{{ rtrim(url('admin/management/products'), '/') }}/',

                get deleteRoute() {
                    return this.deleteRouteBase + this.deleteId;
                },

                /*
                |──────────────────────────────────────────────────────────
                | State form CREATE
                |──────────────────────────────────────────────────────────
                */
                createImages: [],
                primaryIndex: 0,

                /*
                |──────────────────────────────────────────────────────────
                | State form EDIT
                | Disimpan per produk ID agar tidak konflik antar produk
                |──────────────────────────────────────────────────────────
                | imgState[productId] = {
                |   existingImages    : [{id, url, alt, isPrimary}]
                |   primaryExistingId : string|null   — ID existing image yang jadi primary
                |   deletedIds        : string[]      — ID existing image yang dihapus
                |   newImages         : [{name, url, file}]
                |   primaryNewIndex   : number        — index di newImages yang jadi primary (-1 = none)
                | }
                */
                imgState: @js(
    $products->mapWithKeys(function ($p) {
        $primaryImg = $p->images->firstWhere('is_primary', true);
        return [
            $p->id => [
                // Pakai accessor $img->image dari model ProductImage
                'existingImages' => $p->images
                    ->map(
                        fn($img) => [
                            'id' => $img->id,
                            'url' => $img->image,
                            'alt' => $img->alt_text ?? $p->name,
                            'isPrimary' => (bool) $img->is_primary,
                        ],
                    )
                    ->values(),
                'primaryExistingId' => optional($primaryImg)->id,
                'deletedIds' => [],
                'newImages' => [],
                'primaryNewIndex' => -1,
            ],
        ];
    }),
),

                /*
                |──────────────────────────────────────────────────────────
                | Computed getters
                |──────────────────────────────────────────────────────────
                */

                // Shortcut state produk yang sedang di-edit
                get img() {
                    return this.editId ? this.imgState[this.editId] : null;
                },

                // Existing images yang belum di-mark deleted
                get visibleExisting() {
                    if (!this.img) return [];
                    return this.img.existingImages.filter(
                        i => !this.img.deletedIds.includes(i.id)
                    );
                },

                // Apakah semua gambar (existing + new) kosong?
                get hasNoImages() {
                    if (!this.img) return true;
                    return this.visibleExisting.length === 0 &&
                        this.img.newImages.length === 0;
                },

                /*
                |──────────────────────────────────────────────────────────
                | CREATE actions
                |──────────────────────────────────────────────────────────
                */
                openCreate() {
                    this.createImages = [];
                    this.primaryIndex = 0;
                    this.createOpen = true;
                },

                removeCreateImage(index) {
                    this.createImages.splice(index, 1);

                    if (this.createImages.length === 0) {
                        this.primaryIndex = 0;
                    } else if (index < this.primaryIndex) {
                        this.primaryIndex--;
                    } else if (index === this.primaryIndex) {
                        this.primaryIndex = 0;
                    }

                    this.syncCreateFiles();
                },

                syncCreateFiles() {
                    const input = document.getElementById('create_images');
                    if (!input) return;
                    const dt = new DataTransfer();
                    this.createImages.forEach(i => {
                        if (i.file) dt.items.add(i.file);
                    });
                    input.files = dt.files;
                },

                /*
                |──────────────────────────────────────────────────────────
                | EDIT actions
                |──────────────────────────────────────────────────────────
                */
                openEdit(id) {
                    this.editId = id;
                    this.editOpen = true;
                },

                setPrimaryExisting(id) {
                    if (!this.img) return;
                    this.img.primaryExistingId = id;
                    this.img.primaryNewIndex = -1; // batalkan primary new jika ada
                },

                setPrimaryNew(index) {
                    if (!this.img) return;
                    this.img.primaryNewIndex = index;
                    this.img.primaryExistingId = null; // batalkan primary existing
                },

                removeExisting(id) {
                    if (!this.img) return;
                    id = String(id);

                    if (!this.img.deletedIds.includes(id)) {
                        this.img.deletedIds.push(id);
                    }

                    // Jika yang dihapus adalah primary existing, reset primary
                    if (this.img.primaryExistingId === id) {
                        const remaining = this.visibleExisting.filter(i => i.id !== id);

                        if (remaining.length > 0) {
                            // Pindahkan primary ke gambar existing pertama yang tersisa
                            this.img.primaryExistingId = remaining[0].id;
                        } else if (this.img.newImages.length > 0) {
                            // Tidak ada existing lagi, pindah ke gambar baru pertama
                            this.img.primaryExistingId = null;
                            this.img.primaryNewIndex = 0;
                        } else {
                            // Tidak ada gambar sama sekali
                            this.img.primaryExistingId = null;
                        }
                    }
                },

                removeNew(index) {
                    if (!this.img) return;
                    this.img.newImages.splice(index, 1);

                    // Sesuaikan primaryNewIndex
                    if (this.img.newImages.length === 0) {
                        // Tidak ada gambar baru lagi
                        this.img.primaryNewIndex = -1;

                        // Jika tidak ada existing juga, primary = null
                        // Jika masih ada existing, kembalikan primary ke existing pertama
                        if (this.visibleExisting.length > 0 && this.img.primaryExistingId === null) {
                            this.img.primaryExistingId = this.visibleExisting[0].id;
                        }
                    } else if (this.img.primaryNewIndex === index) {
                        this.img.primaryNewIndex = 0; // pindah ke new pertama
                    } else if (this.img.primaryNewIndex > index) {
                        this.img.primaryNewIndex--;
                    }

                    this.syncEditFiles();
                },

                syncEditFiles() {
                    if (!this.editId) return;
                    const input = document.getElementById(`edit_images_${this.editId}`);
                    if (!input) return;
                    const dt = new DataTransfer();
                    this.img.newImages.forEach(i => dt.items.add(i.file));
                    input.files = dt.files;
                },

                handleEditFileChange(event) {
                    Array.from(event.target.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const wasEmpty = this.hasNoImages;

                            this.img.newImages.push({
                                name: file.name,
                                url: e.target.result,
                                file,
                            });

                            // Jika sebelumnya tidak ada gambar sama sekali,
                            // otomatis jadikan gambar baru ini sebagai primary
                            if (wasEmpty || (this.img.primaryExistingId === null && this.img.primaryNewIndex ===
                                    -1)) {
                                this.img.primaryNewIndex = 0;
                                this.img.primaryExistingId = null;
                            }

                            this.syncEditFiles();
                        };
                        reader.readAsDataURL(file);
                    });
                    // Reset value agar file yang sama bisa dipilih lagi
                    event.target.value = '';
                },

                /*
                |──────────────────────────────────────────────────────────
                | DELETE actions
                |──────────────────────────────────────────────────────────
                */
                openDelete(id, name) {
                    this.deleteId = id;
                    this.deleteName = name;
                    this.deleteOpen = true;
                },

                submitDelete() {
                    // Submit form dengan action yang benar
                    const form = this.$el.closest('[x-show="deleteOpen"]').querySelector('form');
                    if (form) {
                        form.action = this.deleteRoute;
                        form.submit();
                    }
                },

            };
        }
    </script>

</x-layout-admin>
