<x-layout-admin>
    <div class="space-y-5">

        {{-- Header + Back --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-medium text-gray-900">Detail Pembayaran</h2>
                <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap transaksi & pesanan terkait</p>
            </div>
            <a href="{{ route('admin.payment.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Kolom kiri: Info transaksi + Info pelanggan --}}
            <div class="space-y-5 lg:col-span-1">

                {{-- Info Transaksi --}}
                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Informasi Pembayaran</p>
                    </div>
                    <div class="px-5 py-4 space-y-3.5">

                        {{-- Gateway Order ID --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Gateway Order ID</span>
                            <span class="font-mono text-xs font-semibold text-gray-900 text-right break-all">
                                {{ $paymentTransaction->gateway_order_id ?? '-' }}
                            </span>
                        </div>

                        {{-- Metode --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Metode</span>
                            <span class="text-xs text-gray-700 text-right">
                                {{ $paymentTransaction->payment_method ?? '-' }}
                            </span>
                        </div>

                        {{-- Jumlah --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Jumlah</span>
                            <span class="text-sm font-bold text-gray-900">
                                Rp{{ number_format($paymentTransaction->amount ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Status</span>
                            @php
                                $payStatusMap = [
                                    'pending' => ['bg-yellow-50 text-yellow-700 border-yellow-100', 'Pending'],
                                    'paid' => ['bg-green-50 text-green-700 border-green-100', 'Dibayar'],
                                    'failed' => ['bg-red-50 text-red-600 border-red-100', 'Gagal'],
                                    'expired' => ['bg-gray-100 text-gray-500 border-gray-200', 'Expired'],
                                    'refunded' => ['bg-purple-50 text-purple-700 border-purple-100', 'Refunded'],
                                ];
                                $payStyle = $payStatusMap[$paymentTransaction->status] ?? [
                                    'bg-gray-100 text-gray-600 border-gray-200',
                                    ucfirst($paymentTransaction->status),
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $payStyle[0] }}">
                                {{ $payStyle[1] }}
                            </span>
                        </div>

                        {{-- Tanggal dibuat --}}
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs text-gray-400 shrink-0">Tanggal</span>
                            <span class="text-xs text-gray-700 text-right">
                                {{ $paymentTransaction->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        {{-- Tanggal lunas --}}
                        @if ($paymentTransaction->paid_at)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs text-gray-400 shrink-0">Dibayar pada</span>
                                <span class="text-xs text-gray-700 text-right">
                                    {{ \Carbon\Carbon::parse($paymentTransaction->paid_at)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        @endif

                    </div>
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
                                {{ strtoupper(substr($paymentTransaction->order->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $paymentTransaction->order->user->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $paymentTransaction->order->user->email ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @if ($paymentTransaction->order->user->phone ?? false)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $paymentTransaction->order->user->phone }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Kolom kanan: Item pesanan --}}
            <div class="lg:col-span-2">

                <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">
                            Pesanan #{{ $paymentTransaction->order->id ?? '-' }}
                        </p>
                        @if ($paymentTransaction->order)
                            <span class="text-xs text-gray-400">
                                {{ $paymentTransaction->order->created_at->format('d M Y') }}
                            </span>
                        @endif
                    </div>

                    {{-- Daftar item --}}
                    <div class="divide-y divide-gray-50">
                        @forelse ($paymentTransaction->order->items ?? [] as $item)
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
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $item->qty }} × Rp{{ number_format($item->price, 0, ',', '.') }}
                                    </p>
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
                    @if ($paymentTransaction->order)
                        <div class="border-t border-gray-100 px-5 py-4 space-y-2.5">

                            {{-- Subtotal --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">Subtotal</span>
                                <span class="text-xs text-gray-700">
                                    Rp{{ number_format($paymentTransaction->order->items->sum(fn($i) => $i->qty * $i->price), 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Total --}}
                            <div class="flex items-center justify-between border-t border-gray-100 pt-2.5">
                                <span class="text-sm font-medium text-gray-900">Total Dibayar</span>
                                <span class="text-sm font-bold text-gray-900">
                                    Rp{{ number_format($paymentTransaction->amount ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-layout-admin>
