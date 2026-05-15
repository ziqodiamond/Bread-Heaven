<x-layout-admin>
    <div class="space-y-5">

        {{-- Header halaman --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Order Management</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua pesanan yang masuk</p>
        </div>


        {{-- Filter & Search --}}
        <div class="rounded-xl border border-gray-100 bg-white px-5 py-4">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-3">

                {{-- Search --}}
                <div class="flex-1 min-w-48">
                    <label class="block text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1.5">
                        Cari Invoice / Customer
                    </label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="No. invoice, nama, email..."
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-3 py-2 text-sm text-gray-700 placeholder-gray-400 focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-colors" />
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="min-w-40">
                    <label class="block text-[11px] font-medium uppercase tracking-wider text-gray-400 mb-1.5">
                        Status
                    </label>
                    <select name="status"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-colors appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol filter --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        Filter
                    </button>

                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.orders.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Tabel Order --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Card header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar Order</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $orders->total() }} order ditemukan</p>
                </div>
            </div>

            {{-- Scroll horizontal untuk mobile --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Invoice</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Customer</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Total</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Payment</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Status</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Tanggal</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Invoice number --}}
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs font-medium text-gray-700">
                                        {{ $order->invoice_number }}
                                    </span>
                                </td>

                                {{-- Customer info --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-medium text-blue-700">
                                            {{ strtoupper(substr($order->customer_name ?? ($order->user?->name ?? 'U'), 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 leading-none">
                                                {{ $order->customer_name ?? ($order->user?->name ?? '-') }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $order->customer_email ?? ($order->user?->email ?? '-') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-3.5">
                                    <span class="font-medium text-gray-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Payment status --}}
                                <td class="px-5 py-3.5">
                                    @php
                                        $paymentColors = [
                                            'paid' => 'bg-green-50 text-green-700',
                                            'pending' => 'bg-yellow-50 text-yellow-700',
                                            'failed' => 'bg-red-50 text-red-700',
                                            'refunded' => 'bg-purple-50 text-purple-700',
                                        ];
                                        $paymentColor =
                                            $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $paymentColor }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>

                                {{-- Order status --}}
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-50 text-yellow-700',
                                            'processing' => 'bg-blue-50 text-blue-700',
                                            'shipped' => 'bg-indigo-50 text-indigo-700',
                                            'completed' => 'bg-green-50 text-green-700',
                                            'cancelled' => 'bg-red-50 text-red-700',
                                            'refunded' => 'bg-purple-50 text-purple-700',
                                        ];
                                        $statusColor =
                                            $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-5 py-3.5 text-gray-500 text-xs">
                                    {{ $order->created_at->format('d M Y') }}
                                    <span class="block text-gray-400">{{ $order->created_at->format('H:i') }}</span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Tidak ada order ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($orders->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

    </div>
</x-layout-admin>
