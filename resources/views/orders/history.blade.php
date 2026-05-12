<x-layout>

    <div class="mx-auto max-w-7xl px-4 py-10">

        {{-- Header --}}
        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Riwayat Pesanan
            </h1>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Semua pesanan yang pernah dibuat
            </p>

        </div>

        {{-- Empty State --}}
        @if ($orders->isEmpty())

            <div
                class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-neutral-700 dark:bg-neutral-900">

                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Belum ada pesanan
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Pesanan yang dibuat akan muncul di sini
                </p>

            </div>
        @else
            <div class="space-y-6">

                @foreach ($orders as $order)
                    <div
                        class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">

                        {{-- Header Card --}}
                        <div
                            class="flex flex-col gap-4 border-b border-gray-100 p-6 dark:border-neutral-800 md:flex-row md:items-center md:justify-between">

                            <div>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Invoice
                                </p>

                                <h2 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $order->invoice_number }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </p>

                            </div>

                            <div class="flex flex-wrap items-center gap-3">

                                {{-- Payment Status --}}
                                <span @class([
                                    'inline-flex rounded-full px-4 py-2 text-xs font-semibold',
                                
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' => in_array(
                                        $order->payment_status,
                                        ['unpaid', 'pending']),
                                
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' =>
                                        $order->payment_status === 'paid',
                                
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' => in_array(
                                        $order->payment_status,
                                        ['failed', 'expired']),
                                ])>

                                    {{ strtoupper($order->payment_status) }}

                                </span>

                                {{-- Order Status --}}
                                <span
                                    class="inline-flex rounded-full bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 dark:bg-neutral-800 dark:text-gray-300">

                                    {{ strtoupper($order->order_status) }}

                                </span>

                            </div>

                        </div>

                        {{-- Items --}}
                        <div class="divide-y divide-gray-100 dark:divide-neutral-800">

                            @foreach ($order->items as $item)
                                <div class="flex items-center gap-4 p-6">

                                    <img src="{{ $item->product?->thumbnail ?? 'https://placehold.co/100x100' }}"
                                        alt="{{ $item->product_name }}" class="h-20 w-20 rounded-2xl object-cover">

                                    <div class="min-w-0 flex-1">

                                        <h3 class="truncate font-semibold text-gray-900 dark:text-white">
                                            {{ $item->product_name }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->quantity }} x
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                        {{-- Footer --}}
                        <div
                            class="flex flex-col gap-4 border-t border-gray-100 p-6 dark:border-neutral-800 md:flex-row md:items-center md:justify-between">

                            <div>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Total Pembayaran
                                </p>

                                <h3 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </h3>

                            </div>

                            <div class="flex flex-wrap items-center gap-3">

                                <a href="{{ route('orders.show', $order->id) }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-800">

                                    Detail Pesanan

                                </a>

                                @if (in_array($order->payment_status, ['unpaid', 'pending']))
                                    <a href="{{ route('payment.show', $order->id) }}"
                                        class="inline-flex items-center justify-center rounded-2xl bg-black px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90 dark:bg-white dark:text-black">

                                        Bayar Sekarang

                                    </a>
                                @endif

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</x-layout>
