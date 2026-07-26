<x-layout-admin>
    <div class="space-y-5">

        {{-- Header + Back --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-medium text-gray-900">Detail Pengiriman</h2>
                <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap shipment & pesanan terkait</p>
            </div>
            <a href="{{ route('admin.shipments.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
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

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Kolom kiri: Info shipment + Info pelanggan --}}
            <div class="space-y-5 lg:col-span-1">

                {{-- Info Shipment --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Informasi Pengiriman</p>
                    </div>
                    <div class="px-5 py-4 space-y-3.5">

                        {{-- Nomor resi --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">No. Resi</span>
                            <span class="font-mono text-xs font-semibold text-gray-900 text-right">
                                {{ $shipment->tracking_number ?? '-' }}
                            </span>
                        </div>

                        {{-- Kurir --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Kurir</span>
                            <span class="text-xs text-gray-700 text-right">{{ $shipment->courier ?? '-' }}</span>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Status</span>
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
                        </div>

                        {{-- Tanggal dibuat --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Dibuat</span>
                            <span class="text-xs text-gray-700 text-right">
                                {{ $shipment->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        {{-- Tanggal terkirim --}}
                        @if ($shipment->delivered_at)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs text-gray-400 shrink-0">Terkirim</span>
                                <span class="text-xs text-gray-700 text-right">
                                    {{ \Carbon\Carbon::parse($shipment->delivered_at)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        @endif

                    </div>

                    {{-- Form input resi --}}
                    @if ($shipment->status === 'pending')
                        <div class="border-t border-gray-100 px-5 py-4">
                            <form action="{{ route('admin.shipment.tracking', $shipment->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="space-y-3">
                                    <div>
                                        <label for="tracking_number" class="block text-xs font-medium text-gray-700 mb-1.5">
                                            Masukkan Nomor Resi
                                        </label>
                                        <input 
                                            type="text" 
                                            id="tracking_number" 
                                            name="tracking_number"
                                            value="{{ old('tracking_number') }}"
                                            placeholder="Contoh: JKT123456789"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-colors"
                                        />
                                        @error('tracking_number')
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button 
                                        type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Simpan Resi & Kirim
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Action buttons --}}
                    @if (!in_array($shipment->status, ['delivered', 'cancelled']))
                        <div class="flex gap-2 px-5 pb-5">
                            <form action="{{ route('admin.shipments.delivered', $shipment->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Tandai sebagai terkirim?')) $el.submit()" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tandai Terkirim
                                </button>
                            </form>

                            <form action="{{ route('admin.shipments.cancel', $shipment->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Batalkan shipment ini?')) $el.submit()" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-100 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batalkan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Info Pelanggan --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Pelanggan</p>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3 mb-3.5">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-semibold text-blue-700">
                                {{ strtoupper(substr($shipment->order->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $shipment->order->user->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $shipment->order->user->email ?? '-' }}</p>
                            </div>
                        </div>
                        @if ($shipment->order->user->phone ?? false)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $shipment->order->user->phone }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Kolom kanan: Item pesanan --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Info Order --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">Pesanan #{{ $shipment->order->id ?? '-' }}</p>
                        @if ($shipment->order)
                            <span class="text-xs text-gray-400">
                                {{ $shipment->order->created_at->format('d M Y') }}
                            </span>
                        @endif
                    </div>

                    {{-- Daftar item --}}
                    <div class="divide-y divide-gray-50">
                        @forelse ($shipment->order->items ?? [] as $item)
                            <div class="flex items-center gap-4 px-5 py-3.5">

                                {{-- Thumbnail produk --}}
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                                    @if ($item->product?->image ?? false)
                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                            alt="{{ $item->product->name }}"
                                            class="h-full w-full rounded-lg object-cover" />
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->qty }} ×
                                        Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-sm font-semibold text-gray-900">
                                        Rp{{ number_format($item->qty * $item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-400">
                                Tidak ada item dalam pesanan ini.
                            </div>
                        @endforelse
                    </div>

                    {{-- Total order --}}
                    @if ($shipment->order)
                        <div class="border-t border-gray-100 px-5 py-4 flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Pesanan</span>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp{{ number_format($shipment->order->total_amount ?? $shipment->order->items->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-layout-admin>
