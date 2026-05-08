<x-layout-admin>
    <div class="container mx-auto mt-5">
        <h2 class="text-xl font-bold mb-4">Admin Dashboard</h2>

        <!-- Dashboard Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <!-- Total Transactions -->
            <div class="bg-white p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800">Total Transactions</h3>
                <p class="text-2xl font-bold">{{ $totalTransactions }}</p>
            </div>

            <!-- Items Sold -->
            <div class="bg-white p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800">Items Sold</h3>
                <p class="text-2xl font-bold">{{ $totalItemsSold }}</p>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white p-4 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800">Total Revenue</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Today's Transactions Table -->
        <h3 class="text-lg font-semibold mb-4">Today's Transactions</h3>
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Total Price</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Payment Status</th>
                    <th class="px-4 py-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactionsToday as $transaction)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-4 py-2">{{ $transaction->id }}</td>
                        <td class="px-4 py-2">{{ $transaction->user->name }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ ucfirst($transaction->status) }}</td>
                        <td class="px-4 py-2">{{ ucfirst($transaction->payment_status) }}</td>
                        <td class="px-4 py-2">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </x-layout-adm>
