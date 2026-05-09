<x-layout>

    <div class="space-y-6 m-6 text-gray-900">

        {{-- ─── Header ─── --}}
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Alamat Saya</h1>
                <p class="mt-0.5 text-sm text-gray-500">
                    Kelola alamat pengiriman kamu
                </p>
            </div>

            <a href="{{ route('address.create') }}"
                class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Alamat
            </a>
        </div>

        {{-- ─── Flash Message ─── --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="flex items-center gap-3 rounded-xl border border-green-100 bg-green-50 px-4 py-3">
                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
                <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- ─── Empty State ─── --}}
        @if ($addresses->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-800">Belum ada alamat tersimpan</h3>
                <p class="mt-1 text-xs text-gray-500">Tambahkan alamat pengiriman untuk memudahkan checkout</p>
                <a href="{{ route('address.create') }}"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Alamat Pertama
                </a>
            </div>
        @else
            {{-- ─── Address Cards ─── --}}
            <div class="space-y-3">
                @foreach ($addresses as $address)
                    <div
                        class="group rounded-xl border bg-white overflow-hidden transition-all duration-200
                               {{ $address->is_default ? 'border-gray-900 shadow-sm' : 'border-gray-100 hover:border-gray-200 hover:shadow-sm' }}">

                        {{-- Badge default --}}
                        @if ($address->is_default)
                            <div class="flex items-center gap-1.5 border-b border-gray-900/10 bg-gray-900 px-4 py-1.5">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs font-medium text-white">Alamat Utama</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-4">

                                {{-- Info alamat --}}
                                <div class="flex gap-3 min-w-0">
                                    {{-- Icon --}}
                                    <div
                                        class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg
                                                {{ $address->is_default ? 'bg-gray-900' : 'bg-gray-100' }}">
                                        <svg class="w-4 h-4 {{ $address->is_default ? 'text-white' : 'text-gray-500' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        {{-- Nama & phone --}}
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ $address->receiver_name }}
                                            </span>
                                            <span class="text-xs text-gray-400">·</span>
                                            <span class="text-xs text-gray-500">{{ $address->receiver_phone }}</span>
                                        </div>

                                        {{-- Alamat lengkap --}}
                                        <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                                            {{ $address->full_address }}
                                        </p>

                                        {{-- Wilayah --}}
                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ $address->district }}, {{ $address->city }},
                                            {{ $address->province }} {{ $address->postal_code }}
                                        </p>

                                        {{-- Notes --}}
                                        @if ($address->notes)
                                            <div class="mt-2 flex items-start gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 flex-shrink-0"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                </svg>
                                                <p class="text-xs text-gray-500 italic">{{ $address->notes }}</p>
                                            </div>
                                        @endif

                                        {{-- Koordinat GPS (jika ada) --}}
                                        @if ($address->latitude && $address->longitude)
                                            <div class="mt-2 flex items-center gap-1.5">
                                                <div class="h-1.5 w-1.5 rounded-full bg-green-400"></div>
                                                <span class="text-xs text-gray-400 font-mono">
                                                    {{ number_format($address->latitude, 5) }},
                                                    {{ number_format($address->longitude, 5) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action buttons --}}
                                <div class="flex flex-shrink-0 items-center gap-1.5">

                                    {{-- Set default --}}
                                    @if (!$address->is_default)
                                        <form action="{{ route('address.setDefault', $address) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors whitespace-nowrap">
                                                Jadikan Utama
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Edit --}}
                                    <a href="{{ route('address.edit', $address) }}"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    {{-- Hapus --}}
                                    <div x-data="{ open: false }">
                                        <button type="button" @click="open = true"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        {{-- Modal konfirmasi hapus --}}
                                        <div x-show="open" x-transition:enter="ease-out duration-200"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-150"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                            @click.self="open = false">

                                            <div x-show="open" x-transition:enter="ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="w-full max-w-sm rounded-2xl border border-gray-100 bg-white p-6 shadow-2xl">

                                                <div
                                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 mx-auto">
                                                    <svg class="w-6 h-6 text-red-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </div>

                                                <h3 class="mt-4 text-center text-base font-semibold text-gray-900">
                                                    Hapus Alamat?
                                                </h3>
                                                <p class="mt-1.5 text-center text-sm text-gray-500">
                                                    Alamat <span class="font-medium text-gray-700">
                                                        "{{ Str::limit($address->full_address, 40) }}"
                                                    </span> akan dihapus permanen.
                                                </p>

                                                <div class="mt-5 flex gap-3">
                                                    <button type="button" @click="open = false"
                                                        class="flex-1 rounded-lg border border-gray-200 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                        Batal
                                                    </button>

                                                    <form action="{{ route('address.destroy', $address) }}"
                                                        method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full rounded-lg bg-red-500 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors">
                                                            Ya, Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        @endif

    </div>

</x-layout>
