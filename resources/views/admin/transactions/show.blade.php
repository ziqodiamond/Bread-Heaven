<!-- Modal View Transaction -->
<div id="viewTransactionModal"
    class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-2xl w-full p-6">
        <h2 class="text-2xl font-bold mb-4 dark:text-white">Transaction Details</h2>

        <!-- Informasi Utama Transaksi -->
        <p class="mb-2 dark:text-gray-200"><strong>User Name:</strong> {{ $transaction->user->name }}</p>
        <p class="mb-2 dark:text-gray-200"><strong>Transaction ID:</strong> {{ $transaction->id }}</p>
        <p class="mb-2 dark:text-gray-200"><strong>Delivery Method:</strong> {{ $transaction->deliveryMethod->name }}</p>
        <p class="mb-2 dark:text-gray-200"><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
        <p class="mb-2 dark:text-gray-200"><strong>Payment Status:</strong> {{ ucfirst($transaction->payment_status) }}
        </p>
        <p class="mb-4 dark:text-gray-200"><strong>Total Price:</strong> Rp
            {{ number_format($transaction->total_price, 0, ',', '.') }}</p>

        <!-- Detail Produk dalam Transaksi -->
        <h3 class="text-lg font-semibold mb-2 dark:text-white">Transaction Items</h3>
        <table class="w-full mb-4">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Product Name</th>
                    <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Quantity</th>
                    <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Price</th>
                    <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $detail)
                    <tr>
                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ $detail->product->name }}</td>
                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ $detail->quantity }}</td>
                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">Rp
                            {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">Rp
                            {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tombol Tutup -->
        <div class="flex justify-end">
            <button onclick="closeModal('viewTransactionModal')"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Close</button>
        </div>
    </div>
</div>
