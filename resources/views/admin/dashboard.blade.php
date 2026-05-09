<x-layout-admin>
    <div class="space-y-6 ml-6">

        {{-- Header --}}
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Dashboard Admin
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Monitoring statistik toko, order, stok, dan aktivitas terbaru.
                </p>
            </div>

            {{-- Tombol aksi cepat --}}
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary/90 transition">
                    Kelola Order
                </a>

                <a href="#" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 transition">
                    Kelola Produk
                </a>
            </div>
        </div>

        {{-- Statistik utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Total order --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Order
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalOrders) }}
                        </h2>
                    </div>

                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
            </div>

            {{-- Produk terjual --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Produk Terjual
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalItemsSold) }}
                        </h2>
                    </div>

                    <div class="p-3 rounded-xl bg-green-100 text-green-600">
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Pendapatan
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div class="p-3 rounded-xl bg-yellow-100 text-yellow-600">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>

            {{-- Customer --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Customer
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalCustomers) }}
                        </h2>
                    </div>

                    <div class="p-3 rounded-xl bg-pink-100 text-pink-600">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik tambahan --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Statistik order --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Statistik Order
                </h3>

                <div class="space-y-3">

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Pending
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700 font-semibold">
                            {{ $pendingOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Diproses
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700 font-semibold">
                            {{ $processingOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Dikirim
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-cyan-100 text-cyan-700 font-semibold">
                            {{ $shippedOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Selesai
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700 font-semibold">
                            {{ $completedOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Dibatalkan
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700 font-semibold">
                            {{ $cancelledOrders }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Statistik pembayaran --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Status Pembayaran
                </h3>

                <div class="space-y-3">

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Paid
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700 font-semibold">
                            {{ $paidOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Unpaid
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700 font-semibold">
                            {{ $unpaidOrders }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Expired
                        </span>

                        <span class="px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-700 font-semibold">
                            {{ $expiredOrders }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- Statistik stok --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Informasi Stok
                </h3>

                <div class="space-y-3">

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Total Produk
                        </span>

                        <span class="font-bold text-gray-800 dark:text-white">
                            {{ $totalProducts }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Stok Menipis
                        </span>

                        <span class="font-bold text-yellow-500">
                            {{ $lowStockProducts }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-300">
                            Produk Habis
                        </span>

                        <span class="font-bold text-red-600">
                            {{ $outOfStockProducts }}
                        </span>
                    </div>

                </div>
            </div>
        </div>

        {{-- Order terbaru --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Order Hari Ini
                        </h3>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Daftar order terbaru customer.
                        </p>
                    </div>

                    <a href="#" class="text-sm text-primary hover:underline">
                        Lihat Semua
                    </a>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">

                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">

                        <tr>
                            <th class="px-5 py-4">Order ID</th>
                            <th class="px-5 py-4">Customer</th>
                            <th class="px-5 py-4">Total</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Pembayaran</th>
                            <th class="px-5 py-4">Tanggal</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($ordersToday as $order)
                            <tr
                                class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">

                                {{-- ID --}}
                                <td class="px-5 py-4 font-semibold text-gray-800 dark:text-white">
                                    #{{ $order->id }}
                                </td>

                                {{-- Customer --}}
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $order->user?->name ?? '-' }}
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-4 font-semibold text-primary">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>

                                {{-- Status order --}}
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold

                                        @if ($order->status === 'pending') bg-yellow-100 text-yellow-700

                                        @elseif($order->status === 'processing')
                                            bg-blue-100 text-blue-700

                                        @elseif($order->status === 'shipped')
                                            bg-cyan-100 text-cyan-700

                                        @elseif($order->status === 'completed')
                                            bg-green-100 text-green-700

                                        @else
                                            bg-red-100 text-red-700 @endif
                                    ">

                                        {{ ucfirst($order->status) }}

                                    </span>
                                </td>

                                {{-- Status pembayaran --}}
                                <td class="px-5 py-4">

                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold

                                        @if ($order->payment_status === 'paid') bg-green-100 text-green-700

                                        @elseif($order->payment_status === 'pending')
                                            bg-yellow-100 text-yellow-700

                                        @else
                                            bg-red-100 text-red-700 @endif
                                    ">

                                        {{ ucfirst($order->payment_status) }}

                                    </span>

                                </td>

                                {{-- Tanggal --}}
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500">
                                    Belum ada order hari ini.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>
        </div>

    </div>
</x-layout-admin>
