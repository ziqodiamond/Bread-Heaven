<x-layout>

    <div class="mx-auto max-w-6xl px-4 py-10">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <p class="text-sm text-gray-500">
                    Detail Pesanan
                </p>

                <h1 class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $order->invoice_number }}
                </h1>
            </div>

            <div class="flex flex-wrap gap-3">

                {{-- PAYMENT STATUS --}}
                <span @class([
                    'rounded-full px-4 py-2 text-sm font-medium',
                    'bg-green-100 text-green-700' => $order->payment_status === 'paid',
                    'bg-yellow-100 text-yellow-700' => $order->payment_status === 'pending',
                    'bg-red-100 text-red-700' => in_array($order->payment_status, [
                        'failed',
                        'expired',
                    ]),
                    'bg-gray-100 text-gray-700' => $order->payment_status === 'unpaid',
                ])>
                    Pembayaran:
                    {{ ucfirst($order->payment_status) }}
                </span>

                {{-- ORDER STATUS --}}
                <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-700">
                    Order:
                    {{ ucfirst($order->order_status) }}
                </span>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ===================================================== --}}
            {{-- LEFT CONTENT --}}
            {{-- ===================================================== --}}

            <div class="space-y-6 lg:col-span-2">

                {{-- ================================================= --}}
                {{-- PRODUK --}}
                {{-- ================================================= --}}

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">

                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">
                        Produk Pesanan
                    </h2>

                    <div class="space-y-5">

                        @foreach ($order->items as $item)
                            <div class="flex gap-4 border-b border-gray-100 pb-5 dark:border-neutral-800">

                                {{-- IMAGE --}}
                                <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                    class="h-20 w-20 rounded-xl object-cover border border-gray-200 dark:border-neutral-700">

                                {{-- INFO --}}
                                <div class="flex-1">

                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->product_name }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $item->quantity }} pcs
                                        &middot;
                                        {{ number_format($item->total_weight / 1000, 2) }} kg
                                    </p>

                                    <p class="mt-3 text-sm text-gray-500">
                                        Harga satuan
                                    </p>

                                    <p class="font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                    </p>

                                </div>

                                {{-- SUBTOTAL --}}
                                <div class="text-right">

                                    <p class="text-sm text-gray-500">
                                        Subtotal
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

                {{-- ================================================= --}}
                {{-- PENGIRIMAN --}}
                {{-- ================================================= --}}

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">

                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">
                        Informasi Pengiriman
                    </h2>

                    <div class="space-y-4 text-sm">

                        <div>
                            <p class="text-gray-500">Penerima</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $order->shipping_receiver_name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">No HP</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $order->shipping_receiver_phone }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Alamat</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $order->shipping_full_address }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Kurir</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $order->shipping_courier }}
                                -
                                {{ $order->shipping_service }}
                            </p>
                        </div>

                        @if ($order->tracking_number)
                            <div>
                                <p class="text-gray-500">Nomor Resi</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $order->tracking_number }}
                                </p>
                            </div>
                        @endif

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- SIDEBAR --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">

                {{-- ================================================= --}}
                {{-- RINGKASAN --}}
                {{-- ================================================= --}}

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">

                    <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">
                        Ringkasan Pembayaran
                    </h2>

                    <div class="space-y-4 text-sm">

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Subtotal</span>

                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Ongkir</span>

                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($order->service_fee > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Biaya Layanan</span>

                                <span class="font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->service_fee, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        @if ($order->discount_amount > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-red-500">Diskon</span>

                                <span class="font-medium text-red-500">
                                    - Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="border-t border-gray-200 pt-4 dark:border-neutral-800">

                            <div class="flex items-center justify-between">

                                <span class="text-base font-semibold text-gray-900 dark:text-white">
                                    Total
                                </span>

                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ================================================= --}}
                {{-- PAYMENT --}}
                {{-- ================================================= --}}

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">

                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Pembayaran
                    </h2>

                    <div class="space-y-3 text-sm">

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">
                                Metode
                            </span>

                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $order->paymentMethod?->name }}
                            </span>
                        </div>

                        @if ($transaction)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">
                                    Status Gateway
                                </span>

                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst($transaction->transaction_status) }}
                                </span>
                            </div>
                        @endif

                    </div>

                    {{-- BUTTON BAYAR --}}
                    @if (in_array($order->payment_status, ['unpaid', 'pending']))
                        <a href="{{ route('payment.show', $order->id) }}"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-medium text-white transition hover:opacity-90 dark:bg-white dark:text-black">

                            Bayar Sekarang

                        </a>
                    @endif

                    {{-- BUTTON TERIMA BARANG --}}
                    @if ($order->order_status === 'shipped' && $order->payment_status === 'paid')
                        <button
                            onclick="confirmReceiveOrder('{{ route('orders.receive', $order->id) }}', '{{ $order->invoice_number }}')"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700">

                            ✓ Terima Barang

                        </button>
                    @endif

                    @if (in_array($order->payment_status, ['unpaid', 'pending']))
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST">

                            @csrf

                            <button type="submit"
                                class="rounded-2xl bg-red-500 px-5 py-3 text-sm font-semibold text-white">

                                Batalkan Pesanan

                            </button>

                        </form>
                    @endif

                </div>

            </div>

        </div>

    </div>

</x-layout>
