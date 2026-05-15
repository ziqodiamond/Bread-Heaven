<x-layout-admin>
    <div class="space-y-5">

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
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->items->count() }} produk</p>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @foreach ($order->items as $item)
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
                        @endforeach
                    </div>

                    {{-- Ringkasan harga --}}
                    <div class="border-t border-gray-100 px-5 py-4 space-y-2">
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span>Rp
                                {{ number_format($order->subtotal_amount ?? $order->total_amount, 0, ',', '.') }}</span>
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
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Shipment info --}}
                @if ($order->shipments->isNotEmpty())
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
                @if ($order->paymentTransactions->isNotEmpty())
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
                                                {{ $trx->transaction_id ?? '-' }}
                                            </td>
                                            <td class="px-5 py-3 text-gray-600">
                                                {{ $trx->payment_type ?? ($order->paymentMethod?->name ?? '-') }}
                                            </td>
                                            <td class="px-5 py-3 font-medium text-gray-900">
                                                Rp
                                                {{ number_format($trx->amount ?? $order->total_amount, 0, ',', '.') }}
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
                                {{ $order->created_at->format('d M Y') }}
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

                        {{-- Proses order: hanya jika status pending & sudah bayar --}}
                        @if ($order->order_status === 'pending' && $order->payment_status === 'paid')
                            <form action="{{ route('admin.orders.process', $order) }}" method="POST">
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
                            <a href="{{ route('admin.orders.shipment', $order) }}"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M14 4v4h4M9 13h6M9 17h3" />
                                </svg>
                                Input Resi & Kirim
                            </a>
                        @endif

                        {{-- Mark delivered: hanya jika sudah shipped --}}
                        @if ($order->order_status === 'shipped')
                            <form action="{{ route('admin.orders.delivered', $order) }}" method="POST" x-data
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
                            <form action="{{ route('admin.orders.refund', $order) }}" method="POST" x-data
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
                            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" x-data
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

    </div>
</x-layout-admin>
