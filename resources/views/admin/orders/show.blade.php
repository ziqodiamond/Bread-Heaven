<x-layout-admin>
    <div class="space-y-5" x-data="modalData()" @open-shipment.window="openModal()" @keydown.escape.window="closeModal()">

        {{-- Header + Back button --}}
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Orders
                    </a>
                    <span class="text-gray-300">/</span>
                    <span class="text-xs text-gray-400 font-mono">{{ $order->invoice_number }}</span>
                </div>
                <h2 class="text-base font-medium text-gray-900">Detail Order</h2>
                <p class="text-sm text-gray-400 mt-0.5">Invoice: <span
                        class="font-mono font-medium text-gray-600">{{ $order->invoice_number }}</span></p>
            </div>

            {{-- Status badge besar --}}
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'completed' => 'bg-green-50 text-green-700 border-green-200',
                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                    'refunded' => 'bg-purple-50 text-purple-700 border-purple-200',
                ];
                $statusColor = $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
            @endphp
            <span
                class="inline-flex items-center rounded-lg border px-3 py-1.5 text-xs font-medium {{ $statusColor }}">
                {{ ucfirst($order->order_status) }}
            </span>
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

        @if (session('error'))
            <div
                class="flex items-center gap-2.5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Layout 2 kolom: kiri (items + shipment) | kanan (info sidebar) --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- ===== KOLOM KIRI (2/3) ===== --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Items yang dipesan --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Item Pesanan</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->items?->count() ?? 0 }} produk</p>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @forelse ($order->items ?? [] as $item)
                            <div class="flex items-center gap-4 px-5 py-4">

                                {{-- Thumbnail produk --}}
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 overflow-hidden">
                                    @if ($item->product?->thumbnail)
                                        <img src="{{ Storage::url($item->product->thumbnail) }}"
                                            alt="{{ $item->product->name }}" class="h-full w-full object-cover" />
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>

                                {{-- Info produk --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item->product?->name ?? ($item->product_name ?? '-') }}
                                    </p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        Rp
                                        {{ number_format($item->subtotal ?? $item->price * $item->quantity, 0, ',', '.') }}
                                    </p>
                                    @if ($item->is_refunded ?? false)
                                        <span
                                            class="inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-medium text-purple-700">
                                            Refunded
                                        </span>
                                    @endif
                                </div>

                            </div>
                        @empty
                            <div class="px-5 py-4 text-center text-sm text-gray-500">
                                Tidak ada item dalam order ini
                            </div>
                        @endforelse
                    </div>

                    {{-- Ringkasan harga --}}
                    <div class="border-t border-gray-100 px-5 py-4 space-y-2">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span>Rp
                                {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->shipping_cost ?? false)
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Ongkos Kirim</span>
                                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($order->discount_amount ?? false)
                            <div class="flex items-center justify-between text-sm text-green-600">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                            <span class="text-sm font-semibold text-gray-900">Total</span>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($order->grand_total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Shipment info --}}
                @if ($order->shipments?->isNotEmpty())
                    @php $shipment = $order->shipments->last(); @endphp
                    <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">Informasi Pengiriman</p>
                        </div>
                        <div class="px-5 py-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1">Kurir</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ strtoupper($shipment->courier_name) }}
                                    @if ($shipment->courier_service)
                                        <span class="font-normal text-gray-500">-
                                            {{ $shipment->courier_service }}</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1">No. Resi
                                </p>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-mono font-medium text-gray-900">
                                        {{ $shipment->tracking_number }}
                                    </p>
                                    @if ($shipment->tracking_url)
                                        <a href="{{ $shipment->tracking_url }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1">Status
                                    Kirim</p>
                                @php
                                    $shipStatusColors = [
                                        'pending' => 'bg-yellow-50 text-yellow-700',
                                        'shipped' => 'bg-blue-50 text-blue-700',
                                        'delivered' => 'bg-green-50 text-green-700',
                                        'failed' => 'bg-red-50 text-red-700',
                                    ];
                                    $shipColor = $shipStatusColors[$shipment->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $shipColor }}">
                                    {{ ucfirst($shipment->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1">Dikirim
                                    Pada</p>
                                <p class="text-sm text-gray-700">
                                    {{ $shipment->shipped_at?->format('d M Y H:i') ?? '-' }}
                                </p>
                            </div>
                            @if ($shipment->notes)
                                <div class="col-span-2">
                                    <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1">
                                        Catatan</p>
                                    <p class="text-sm text-gray-600">{{ $shipment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Payment transactions --}}
                @if ($order->paymentTransactions?->isNotEmpty())
                    <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">Riwayat Transaksi</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th
                                            class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                            Transaction ID</th>
                                        <th
                                            class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                            Metode</th>
                                        <th
                                            class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                            Jumlah</th>
                                        <th
                                            class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                            Status</th>
                                        <th
                                            class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                            Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($order->paymentTransactions as $trx)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-5 py-3 font-mono text-xs text-gray-600">
                                                {{ $trx->gateway_transaction_id ?? '-' }}
                                            </td>
                                            <td class="px-5 py-3 text-gray-600">
                                                {{ $trx->payment_type ?? ($order->paymentMethod?->name ?? '-') }}
                                            </td>
                                            <td class="px-5 py-3 font-medium text-gray-900">
                                                Rp
                                                {{ number_format($trx->gross_amount ?? $order->grand_total, 0, ',', '.') }}
                                            </td>
                                            <td class="px-5 py-3">
                                                @php
                                                    $trxColors = [
                                                        'settlement' => 'bg-green-50 text-green-700',
                                                        'capture' => 'bg-green-50 text-green-700',
                                                        'pending' => 'bg-yellow-50 text-yellow-700',
                                                        'deny' => 'bg-red-50 text-red-700',
                                                        'cancel' => 'bg-red-50 text-red-700',
                                                        'expire' => 'bg-gray-100 text-gray-600',
                                                        'refund' => 'bg-purple-50 text-purple-700',
                                                    ];
                                                    $trxColor =
                                                        $trxColors[$trx->transaction_status ?? ''] ??
                                                        'bg-gray-100 text-gray-600';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $trxColor }}">
                                                    {{ ucfirst($trx->transaction_status ?? '-') }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-xs text-gray-500">
                                                {{ $trx->created_at?->format('d M Y H:i') ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            {{-- ===== KOLOM KANAN (1/3) - Sidebar ===== --}}
            <div class="space-y-5">

                {{-- Info Customer --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Informasi Customer</p>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-medium text-blue-700">
                                {{ strtoupper(substr($order->customer_name ?? ($order->user?->name ?? 'U'), 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $order->customer_name ?? ($order->user?->name ?? '-') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $order->customer_email ?? ($order->user?->email ?? '-') }}
                                </p>
                            </div>
                        </div>
                        @if ($order->user?->phone ?? ($order->customer_phone ?? false))
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-gray-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $order->user?->phone ?? $order->customer_phone }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Alamat Pengiriman --}}
                @if ($order->address)
                    <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">Alamat Pengiriman</p>
                        </div>
                        <div class="px-5 py-4 space-y-1.5">
                            <p class="text-sm font-medium text-gray-900">{{ $order->address->recipient_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->address->phone }}</p>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                {{ $order->address->address_line }},
                                {{ $order->address->city }},
                                {{ $order->address->province }},
                                {{ $order->address->postal_code }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Metode Pembayaran --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Pembayaran</p>
                    </div>
                    <div class="px-5 py-4 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Metode</span>
                            <span class="text-sm font-medium text-gray-700">
                                {{ $order->paymentMethod?->name ?? '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Status</span>
                            @php
                                $paymentColors = [
                                    'paid' => 'bg-green-50 text-green-700',
                                    'pending' => 'bg-yellow-50 text-yellow-700',
                                    'failed' => 'bg-red-50 text-red-700',
                                    'refunded' => 'bg-purple-50 text-purple-700',
                                ];
                                $pColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $pColor }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Tanggal Order</span>
                            <span class="text-sm text-gray-700">
                                {{ $order->created_at?->format('d M Y') ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Aksi Admin --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Aksi</p>
                    </div>
                    <div class="px-5 py-4 space-y-2.5">

                        {{-- Mark as paid: jika belum dibayar --}}
                        @if ($order->payment_status !== 'paid' && !in_array($order->order_status, ['cancelled', 'refunded']))
                            <form action="{{ route('admin.orders.mark-as-paid', $order->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Tandai order ini sebagai pembayaran manual?')) $el.submit()">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 hover:bg-amber-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Tandai Sebagai Pembayaran Manual
                                </button>
                            </form>
                        @endif

                        {{-- Proses order: hanya jika status pending & sudah bayar --}}
                        @if ($order->order_status === 'pending' && $order->payment_status === 'paid')
                            <form action="{{ route('admin.orders.process', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Proses Order
                                </button>
                            </form>
                        @endif

                        {{-- Buat shipment: hanya jika status processing --}}
                        @if ($order->order_status === 'processing')
                            <button @click="$dispatch('open-shipment')"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M14 4v4h4M9 13h6M9 17h3" />
                                </svg>
                                Input Resi & Kirim
                            </button>
                        @endif

                        {{-- Mark delivered: hanya jika sudah shipped --}}
                        @if ($order->order_status === 'shipped')
                            <form action="{{ route('admin.orders.delivered', $order->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Tandai order ini sebagai terkirim?')) $el.submit()">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tandai Terkirim
                                </button>
                            </form>
                        @endif

                        {{-- Refund: jika sudah paid tapi belum shipped --}}
                        @if (in_array($order->order_status, ['pending', 'processing', 'completed']) && $order->payment_status === 'paid')
                            <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Proses refund untuk order ini?')) $el.submit()">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-purple-200 px-4 py-2.5 text-sm font-medium text-purple-700 hover:bg-purple-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Refund Order
                                </button>
                            </form>
                        @endif

                        {{-- Cancel: jika belum shipped --}}
                        @if (!in_array($order->order_status, ['shipped', 'completed', 'cancelled', 'refunded']))
                            <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" x-data
                                @submit.prevent="if(confirm('Batalkan order ini?')) $el.submit()">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batalkan Order
                                </button>
                            </form>
                        @endif

                    </div>
                </div>

            </div>
            {{-- end kolom kanan --}}

        </div>

        {{-- Modal Shipment Form (Alpine.js) --}}
        <template x-if="openShipment">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
            aria-modal="true" role="dialog" @click.self="openShipment = false">

            <div x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="translate-y-4 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-4 opacity-0"
                class="w-full max-w-3xl md:max-w-2xl lg:max-w-3xl bg-white rounded-xl border border-gray-200 shadow-xl overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Input Resi & Kirim</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Invoice: {{ $order->invoice_number }}</p>
                    </div>
                    <button @click="openShipment = false"
                        class="flex items-center justify-center size-8 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="px-6 py-5 max-h-[calc(100vh-200px)] overflow-y-auto">

                    {{-- Alamat Pengiriman (Paling Atas) --}}
                    @if ($order->shipping_receiver_name || $order->shipping_full_address)
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex gap-2">
                                <svg class="h-5 w-5 text-blue-600 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1 4.5 4.5 0 11-4.814 6.946z" />
                                </svg>
                                <div class="text-sm flex-1">
                                    <p class="font-medium text-blue-900 mb-2">Alamat Pengiriman</p>
                                    <div class="text-blue-800 space-y-1">
                                        @if ($order->shipping_receiver_name)
                                            <p><strong>{{ $order->shipping_receiver_name }}</strong></p>
                                        @endif
                                        @if ($order->shipping_receiver_phone)
                                            <p>{{ $order->shipping_receiver_phone }}</p>
                                        @endif
                                        @if ($order->shipping_full_address)
                                            <p>{{ $order->shipping_full_address }}</p>
                                        @endif
                                        @if ($order->shipping_city || $order->shipping_postal_code)
                                            <p class="text-blue-600">{{ $order->shipping_city }} {{ $order->shipping_postal_code }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Current Shipping Info (jika ada) --}}
                    @if ($order->shipping_courier || $order->shipping_service || $order->shipping_notes)
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex gap-2">
                                <svg class="h-5 w-5 text-green-600 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-sm flex-1">
                                    <p class="font-medium text-green-900 mb-2">Informasi Pengiriman Saat Ini</p>
                                    <div class="text-green-800 space-y-1 text-xs">
                                        @if ($order->shipping_courier)
                                            <p><strong>Kurir:</strong> {{ $order->shipping_courier }}</p>
                                        @endif
                                        @if ($order->shipping_service)
                                            <p><strong>Layanan:</strong> {{ $order->shipping_service }}</p>
                                        @endif
                                        @if ($order->shipping_notes)
                                            <p><strong>Catatan:</strong> {{ $order->shipping_notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Pilihan metode pengiriman --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-3">Metode Pengiriman</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all hover:border-indigo-300" 
                                :class="method === 'delivery' ? 'bg-indigo-50 border-indigo-300' : 'bg-white hover:bg-gray-50'">
                                <input type="radio" name="method" value="delivery" x-model="method" class="w-4 h-4 accent-indigo-600" checked>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Dikirim</span>
                                    <span class="text-xs text-gray-500">Kurrir Pengiriman</span>
                                </div>
                            </label>
                            <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all hover:border-green-300"
                                :class="method === 'pickup' ? 'bg-green-50 border-green-300' : 'bg-white hover:bg-gray-50'">
                                <input type="radio" name="method" value="pickup" x-model="method" class="w-4 h-4 accent-green-600">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Ambil</span>
                                    <span class="text-xs text-gray-500">Self Pickup</span>
                                </div>
                            </label>
                        </div>
                    </div>

                {{-- Form Dikirim --}}
                <div x-show="method === 'delivery'" x-transition class="space-y-4">
                    <form action="{{ route('admin.orders.shipment', $order->id) }}" method="POST" 
                        onsubmit="const parts = (document.getElementById('courier_name')?.value || '').split('|'); document.getElementById('courier_name_hidden').value = parts[0] || ''; document.getElementById('courier_service_hidden').value = parts[1] || ''; return confirm('Buat shipment dengan data ini?');">

                        @csrf

                        <input type="hidden" name="method" value="delivery">
                        <input type="hidden" name="courier_name" id="courier_name_hidden" value="">
                        <input type="hidden" name="courier_service" id="courier_service_hidden" value="">

                        {{-- Pilih Kurir & Layanan --}}
                        <div>
                            <label for="courier_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Kurir & Layanan <span class="text-red-500">*</span>
                            </label>
                            <select name="courier_name" id="courier_name" x-model="selectedCourier"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm text-gray-900"
                                required>
                                <option value="">-- Pilih Kurir --</option>
                                <template x-for="courier in couriersData" :key="courier.code">
                                    <optgroup :label="courier.name + ' - Layanan:'">
                                        <template x-for="service in courier.services" :key="service.code">
                                            <option :value="`${courier.code}|${service.code}`" 
                                                x-text="`${courier.name} - ${service.name}`">
                                            </option>
                                        </template>
                                    </optgroup>
                                </template>
                            </select>
                            @error('courier_name')
                                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Biaya Pengiriman --}}
                        <template x-if="selectedCourier">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-amber-900">Estimasi Biaya Pengiriman</span>
                                    <span class="text-lg font-bold text-amber-900" x-text="`Rp ${new Intl.NumberFormat('id-ID').format({{ $order->shipping_cost ?? 0 }})}`"></span>
                                </div>
                                <p class="text-xs text-amber-700 mt-2">*Biaya mungkin berubah sesuai dengan layanan yang dipilih</p>
                            </div>
                        </template>

                        {{-- Input Nomor Resi --}}
                        <div>
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Resi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tracking_number" id="tracking_number"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm text-gray-900"
                                placeholder="Contoh: 1234567890ABCD" required>
                            @error('tracking_number')
                                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan (Optional) --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <textarea name="notes" id="notes"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm text-gray-900 resize-none"
                                placeholder="Catatan tambahan untuk pengiriman..." rows="2" maxlength="500"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Max 500 karakter</p>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2 justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="openShipment = false" :disabled="loading"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading"
                                class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span x-show="!loading">Simpan & Kirim</span>
                                <span x-show="loading" class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Form Ambil di Tempat --}}
                <div x-show="method === 'pickup'" x-transition class="space-y-4">
                    <form action="{{ route('admin.orders.shipment', $order->id) }}" method="POST" onsubmit="return confirm('Tandai order siap diambil?');">
                        @csrf

                        <input type="hidden" name="method" value="pickup">
                        <input type="hidden" name="courier_name" value="self-pickup">
                        <input type="hidden" name="courier_service" value="Ambil di Tempat">
                        <input type="hidden" name="tracking_number" value="PICKUP">

                        {{-- Catatan (Optional) --}}
                        <div>
                            <label for="notes_pickup" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <textarea name="notes" id="notes_pickup"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors text-sm text-gray-900 resize-none"
                                placeholder="Contoh: Diletakkan di loket... atau waktu tersedia..." rows="2" maxlength="500"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Max 500 karakter</p>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2 justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="openShipment = false" :disabled="loading"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading"
                                class="px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span x-show="!loading">Tandai Siap Diambil</span>
                                <span x-show="loading" class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </template>

    <script>
        window.COURIERS_DATA = @json($couriers ?? []);
    </script>

</div>

</x-layout-admin>
