{{-- resources/views/admin/management/store/index.blade.php --}}

<x-layout-admin>

    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">
                Store Management
            </h2>

            <p class="mt-0.5 text-sm text-gray-400">
                Kelola semua toko, pickup, dan origin shipping
            </p>
        </div>



        {{-- ── Tabel ─────────────────────────────────────────────────── --}}
        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">

            {{-- Header table --}}
            <div
                class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-900">
                        Daftar Toko
                    </p>

                    <p class="mt-0.5 text-xs text-gray-400">
                        {{ $stores->count() }} toko terdaftar
                    </p>
                </div>

                <div class="flex items-center gap-3">

                    {{-- Search --}}
                    <div class="relative">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />

                        </svg>

                        <input type="text" placeholder="Cari toko..."
                            class="block w-full rounded-lg border border-gray-200 py-2 pl-8 pr-3 text-sm
                                   text-gray-700 placeholder-gray-400 focus:border-gray-400
                                   focus:outline-none focus:ring-0">

                    </div>

                    {{-- Button --}}
                    <a href="{{ route('admin.management.stores.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2
                               text-sm font-medium text-white transition-colors hover:bg-gray-700">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4v16m8-8H4" />

                        </svg>

                        Tambah Toko

                    </a>

                </div>

            </div>



            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    {{-- Head --}}
                    <thead>

                        <tr class="border-b border-gray-100">

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Toko
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Kontak
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Lokasi
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Pickup
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Origin
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Status
                            </th>

                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Aksi
                            </th>

                        </tr>

                    </thead>



                    {{-- Body --}}
                    <tbody class="divide-y divide-gray-50">

                        @forelse ($stores as $store)
                            <tr class="transition-colors hover:bg-gray-50">

                                {{-- Store --}}
                                <td class="px-5 py-3.5">

                                    <div class="flex items-center gap-3">

                                        {{-- Logo --}}
                                        @if ($store->logo)
                                            <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}"
                                                class="h-10 w-10 rounded-xl border border-gray-100 object-cover">
                                        @else
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-400">
                                                No
                                            </div>
                                        @endif

                                        {{-- Name --}}
                                        <div>

                                            <p class="font-medium text-gray-900">
                                                {{ $store->name }}
                                            </p>

                                            <p class="mt-0.5 text-[11px] text-gray-400">
                                                {{ $store->slug }}
                                            </p>

                                        </div>

                                    </div>

                                </td>



                                {{-- Kontak --}}
                                <td class="px-5 py-3.5 text-gray-600">

                                    <p>
                                        {{ $store->phone ?: '-' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-gray-400">
                                        {{ $store->email ?: '-' }}
                                    </p>

                                </td>



                                {{-- Lokasi --}}
                                <td class="px-5 py-3.5 text-gray-600">

                                    <p>
                                        {{ $store->city }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-gray-400">
                                        {{ $store->province }}
                                    </p>

                                </td>



                                {{-- Pickup --}}
                                <td class="px-5 py-3.5">

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $store->allow_pickup ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">

                                        {{ $store->allow_pickup ? 'Aktif' : 'Nonaktif' }}

                                    </span>

                                </td>



                                {{-- Origin --}}
                                <td class="px-5 py-3.5">

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $store->is_shipping_origin ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">

                                        {{ $store->is_shipping_origin ? 'Ya' : 'Tidak' }}

                                    </span>

                                </td>



                                {{-- Status --}}
                                <td class="px-5 py-3.5">

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $store->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">

                                        {{ $store->is_active ? 'Aktif' : 'Nonaktif' }}

                                    </span>

                                </td>



                                {{-- Aksi --}}
                                <td class="px-5 py-3.5">

                                    <div class="flex items-center gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.management.stores.edit', $store->id) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100
                                                   px-2.5 py-1.5 text-xs font-medium text-blue-700
                                                   transition-colors hover:bg-blue-50">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                       m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />

                                            </svg>

                                            Edit

                                        </a>



                                        {{-- Delete --}}
                                        <button
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-100
                                                   px-2.5 py-1.5 text-xs font-medium text-red-600
                                                   transition-colors hover:bg-red-50">

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

                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">

                                    Belum ada toko.
                                    Klik <strong>Tambah Toko</strong> untuk mulai.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

</x-layout-admin>
