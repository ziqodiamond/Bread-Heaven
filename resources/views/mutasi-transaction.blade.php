<x-layout>
    <div class="container mx-auto mt-5">
        <h2 class="text-2xl font-bold mb-4">Transaction History</h2>

        <!-- Check if there are any transactions -->
        @if ($transactions->isEmpty())
            <p class="text-center text-gray-500 dark:text-gray-300">You have no transactions yet.</p>
        @else
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <table class="w-full mb-4">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Transaction ID</th>
                            <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Status</th>
                            <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Total Price</th>
                            <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Date</th>
                            <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ $transaction->id }}
                                </td>
                                <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">
                                    <span
                                        class="px-4 py-1 rounded-full text-white 
                                        @if ($transaction->status === 'pending') bg-yellow-500
                                        @elseif($transaction->status === 'process') bg-blue-500
                                        @elseif($transaction->status === 'shipped') bg-indigo-500
                                        @elseif($transaction->status === 'completed') bg-green-500
                                        @else bg-red-500 @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">
                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                </td>
                                <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">
                                    {{ $transaction->created_at->format('d M Y') }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('transaction.show', $transaction->id) }}"
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layout>
