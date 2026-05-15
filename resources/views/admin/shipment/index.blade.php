<x-layout-admin>
    <div class="space-y-5">

        {{-- Header halaman --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Shipment Management</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua pengiriman pesanan</p>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2.5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Card header + Search --}}
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar Pengiriman</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $shipments->total() }} total pengiriman</p>
                </div>

                {{-- Search form --}}
                <form method="GET" action="{{ route('admin.shipment.index') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari no. resi..."
                            class="w-56 rounded-lg border border-gray-200 py-1.5 pl-8 pr-3 text-xs text-gray-700 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-1 focus:ring-blue-100" />
                    </div>
                    <button type="submit"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-700 transition-colors">
                        Cari
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.shipment.index') }}"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel scroll horizontal --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                No. Resi</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Order ID</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Kurir</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Status</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Tanggal</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($shipments as $shipment)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Nomor resi --}}
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs font-medium text-gray-900">
                                        {{ $shipment->tracking_number ?? '-' }}
                                    </span>
                                </td>

                                {{-- Order ID --}}
                                <td class="px-5 py-3.5 text-gray-500 text-xs">
                                    #{{ $shipment->order->id ?? '-' }}
                                </td>

                                {{-- Kurir --}}
                                <td class="px-5 py-3.5 text-gray-700 text-xs">
                                    {{ $shipment->courier ?? '-' }}
                                </td>

                                {{-- Badge status --}}
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusMap = [
                                            'pending' => ['bg-yellow-50 text-yellow-700 border-yellow-100', 'Pending'],
                                            'shipped' => ['bg-blue-50 text-blue-700 border-blue-100', 'Dikirim'],
                                            'delivered' => ['bg-green-50 text-green-700 border-green-100', 'Terkirim'],
                                            'cancelled' => ['bg-red-50 text-red-600 border-red-100', 'Dibatalkan'],
                                        ];
                                        $statusStyle = $statusMap[$shipment->status] ?? [
                                            'bg-gray-100 text-gray-600 border-gray-200',
                                            ucfirst($shipment->status),
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusStyle[0] }}">
                                        {{ $statusStyle[1] }}
                                    </span>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-5 py-3.5 text-xs text-gray-400">
                                    {{ $shipment->created_at->format('d M Y') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">

                                        {{-- Detail --}}
                                        <a href="{{ route('admin.shipment.show', $shipment->id) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>

                                        {{-- Mark delivered — hanya jika bukan delivered/cancelled --}}
                                        @if (!in_array($shipment->status, ['delivered', 'cancelled']))
                                            <form action="{{ route('admin.shipment.delivered', $shipment->id) }}"
                                                method="POST" x-data
                                                @submit.prevent="if(confirm('Tandai shipment ini sebagai terkirim?')) $el.submit()">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-green-100 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Terkirim
                                                </button>
                                            </form>

                                            {{-- Cancel --}}
                                            <form action="{{ route('admin.shipment.cancel', $shipment->id) }}"
                                                method="POST" x-data
                                                @submit.prevent="if(confirm('Batalkan shipment ini?')) $el.submit()">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                    Belum ada data pengiriman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($shipments->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $shipments->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layout-admin>
