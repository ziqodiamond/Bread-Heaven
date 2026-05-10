{{-- Modal Cart — Alpine.js controlled --}}
<div x-data="{ open: false }" x-show="open" x-on:open-cart.window="open = true" x-on:keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50" aria-modal="true"
    role="dialog" @click.self="open = false" {{-- klik backdrop menutup modal --}}>
    <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-4 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-gray-800 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Keranjang</span>
                <span
                    class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">
                    {{ $cartItems->count() }} item
                </span>
            </div>
            {{-- Tombol tutup --}}
            <button @click="open = false"
                class="flex items-center justify-center size-7 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Daftar Item --}}
        <ul class="divide-y divide-gray-100 dark:divide-gray-800 max-h-72 overflow-y-auto">
            @forelse ($cartItems as $item)
                <li class="flex items-center gap-3 px-5 py-3.5">

                    {{-- Thumbnail produk --}}
                    <div
                        class="size-13 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800 flex-shrink-0 bg-gray-50 dark:bg-gray-800">
                        @if ($item->product?->thumbnail)
                            <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}"
                                class="size-full object-cover" />
                        @else
                            <img src="{{ asset('images/default-product.jpg') }}" alt="Default"
                                class="size-full object-cover" />
                        @endif
                    </div>

                    {{-- Info produk --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $item->product->name ?? 'Produk tidak tersedia' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            Qty: {{ $item->quantity }}
                        </p>
                    </div>

                    {{-- Harga & Hapus --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            Rp {{ number_format($item->product->price ?? 0, 0, ',', '.') }}
                        </span>
                        {{-- Form hapus — tanpa AJAX, pakai POST biasa --}}
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center justify-center size-7 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-red-500 hover:border-red-200 dark:hover:border-red-800 transition">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>

                </li>
            @empty
                {{-- State kosong --}}
                <li class="py-10 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Keranjang masih kosong.</p>
                </li>
            @endforelse
        </ul>

        {{-- Subtotal --}}
        @if ($cartItems->count())
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                <span class="text-xs text-gray-500 dark:text-gray-400">Subtotal</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    Rp
                    {{ number_format($cartItems->sum(fn($i) => ($i->product->price ?? 0) * $i->quantity), 0, ',', '.') }}
                </span>
            </div>
        @endif

        {{-- Actions --}}
        <div class="px-5 py-4 flex flex-col gap-2 border-t border-gray-100 dark:border-gray-800">

            <a href="{{ route('checkout.index') }}"
                class="block text-center text-sm font-medium text-white bg-gray-900 dark:bg-white dark:text-gray-900 rounded-lg px-4 py-2.5 hover:opacity-90 transition">
                Checkout
            </a>

            <a href="/cart"
                class="block text-center text-sm text-gray-500 dark:text-gray-400 rounded-lg px-4 py-2.5 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Lihat keranjang ({{ $cartItems->count() }})
            </a>
        </div>

    </div>
</div>
