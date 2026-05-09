{{-- resources/views/item.blade.php --}}
<x-layout>
    <div class="mx-auto max-w-5xl px-6 py-10">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">

            {{-- ===== GAMBAR ===== --}}
            <div>
                {{-- Gambar utama --}}
                <div class="aspect-square w-full overflow-hidden rounded-2xl bg-gray-100">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                </div>

                {{-- Thumbnail gallery --}}
                @if ($product->images->count())
                    <div class="mt-3 grid grid-cols-4 gap-2.5">
                        @foreach ($product->images as $image)
                            <div class="aspect-square overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                <img src="{{ $image->image }}" alt="{{ $image->alt_text }}"
                                    class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ===== KONTEN ===== --}}
            <div>
                {{-- Badge kategori --}}
                @if ($product->category)
                    <span
                        class="inline-block rounded-full border border-gray-200 bg-gray-100 px-3 py-1
                                 text-xs text-gray-500">
                        {{ $product->category }}
                    </span>
                @endif

                {{-- Nama produk --}}
                <h1 class="mt-3 text-2xl font-medium leading-snug text-gray-900">
                    {{ $product->name }}
                </h1>

                {{-- SKU --}}
                @if ($product->sku)
                    <p class="mt-1 text-xs text-gray-400">SKU: {{ $product->sku }}</p>
                @endif

                {{-- Harga --}}
                <p class="mt-5 text-2xl font-medium text-gray-900">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                {{-- Status stok --}}
                <div class="mt-3 flex items-center gap-2">
                    @if ($product->in_stock)
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-500">Stok tersedia ({{ $product->stock }})</span>
                    @else
                        <span class="h-2 w-2 rounded-full bg-red-400"></span>
                        <span class="text-sm text-gray-500">Stok habis</span>
                    @endif
                </div>

                <hr class="my-6 border-gray-100">

                {{-- Deskripsi --}}
                @if ($product->description)
                    <p class="text-xs font-medium uppercase tracking-widest text-gray-400">Deskripsi</p>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        {!! nl2br(e($product->description)) !!}
                    </p>
                @endif

                {{-- Tombol aksi --}}
                <div class="mt-6 grid grid-cols-2 gap-2.5">
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl
                                   bg-gray-900 px-5 py-3.5 text-sm font-medium text-white
                                   transition hover:opacity-85">
                            Tambah ke Keranjang
                        </button>
                    </form>

                    {{-- Tombol Beli Sekarang --}}
                    {{-- Tombol Beli Sekarang + Modal Alpine.js --}}
                    <div x-data="{
                        open: false,
                        qty: 1,
                        stock: {{ $product->stock ?? 0 }},
                        price: {{ $product->price }},
                        get total() {
                            return 'Rp ' + (this.qty * this.price).toLocaleString('id-ID')
                        },
                        increment() { if (this.qty < this.stock) this.qty++ },
                        decrement() { if (this.qty > 1) this.qty-- },
                    }">

                        {{-- Trigger button --}}
                        <button type="button" @click="open = true" {{ !$product->in_stock ? 'disabled' : '' }}
                            class="flex w-full items-center justify-center gap-2 rounded-xl
               border border-gray-200 px-5 py-3.5 text-sm font-medium text-gray-700
               transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                            Beli Sekarang
                        </button>

                        {{-- Overlay + Modal --}}
                        <div x-show="open" x-transition:enter="transition duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" @keydown.escape.window="open = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                            style="display: none;">
                            {{-- Klik di luar modal = tutup --}}
                            <div class="absolute inset-0" @click="open = false"></div>

                            {{-- Panel modal --}}
                            <div x-show="open" x-transition:enter="transition duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                                {{-- Header --}}
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">Konfirmasi Pembelian</p>
                                    <button type="button" @click="open = false"
                                        class="rounded-lg p-1 text-gray-400 transition hover:bg-gray-100">
                                        {{-- Heroicon x-mark --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <hr class="my-4 border-gray-100">

                                {{-- Info produk --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                                        class="h-12 w-12 rounded-xl object-cover bg-gray-100 flex-shrink-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 leading-snug">{{ $product->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Input jumlah --}}
                                <label class="text-xs font-medium text-gray-400 uppercase tracking-widest">
                                    Jumlah
                                </label>
                                <div class="mt-2 flex items-center gap-2">
                                    {{-- Tombol kurang --}}
                                    <button type="button" @click="decrement" :disabled="qty <= 1"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border
                           border-gray-200 text-gray-600 transition hover:bg-gray-50
                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                        </svg>
                                    </button>

                                    {{-- Input angka --}}
                                    <input type="number" x-model.number="qty" :min="1"
                                        :max="stock"
                                        @input="qty = Math.min(Math.max(parseInt($event.target.value) || 1, 1), stock)"
                                        class="h-9 w-16 rounded-xl border border-gray-200 text-center text-sm
                           font-medium text-gray-900 focus:outline-none focus:ring-2
                           focus:ring-gray-300">

                                    {{-- Tombol tambah --}}
                                    <button type="button" @click="increment" :disabled="qty >= stock"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border
                           border-gray-200 text-gray-600 transition hover:bg-gray-50
                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 5v14M5 12h14" />
                                        </svg>
                                    </button>

                                    <span class="text-xs text-gray-400 ml-1">/ {{ $product->stock }} tersedia</span>
                                </div>

                                {{-- Ringkasan total --}}
                                <div class="mt-4 flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3">
                                    <span class="text-xs text-gray-500">Total</span>
                                    <span x-text="total" class="text-sm font-medium text-gray-900"></span>
                                </div>

                                {{-- Form submit --}}
                                <form action="{{ route('checkout.buy-now') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    {{-- qty dikirim lewat hidden input yang disync Alpine --}}
                                    <input type="hidden" name="quantity" :value="qty">

                                    <button type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl
                           bg-gray-900 px-5 py-3.5 text-sm font-medium text-white
                           transition hover:opacity-85">
                                        Beli Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                {{-- Spesifikasi dimensi --}}
                <div class="grid grid-cols-4 gap-2">
                    @foreach ([
        'Berat' => ($product->weight ?? 0) . ' gr',
        'Panjang' => ($product->length ?? 0) . ' cm',
        'Lebar' => ($product->width ?? 0) . ' cm',
        'Tinggi' => ($product->height ?? 0) . ' cm',
    ] as $label => $value)
                        <div class="rounded-xl bg-gray-50 p-3">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Status produk --}}
                <div class="mt-4">
                    <span
                        class="inline-block rounded-full px-3 py-1 text-xs font-medium
                        {{ $product->is_available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $product->status }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</x-layout>
