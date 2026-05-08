<x-layout-admin>
    <div class="container mx-auto mt-5">
        <h2 class="text-2xl font-bold mb-4">Transaction Management</h2>

        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('transaction.index') }}" class="mb-4">
            <div class="flex items-center space-x-4">
                <input type="text" name="search" placeholder="Search by user name or email"
                    class="border px-3 py-2 rounded" value="{{ request()->search }}">
                <select name="status" class="border px-3 py-2 rounded">
                    <option value="">Filter by Status</option>
                    <option value="pending" {{ request()->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="process" {{ request()->status === 'process' ? 'selected' : '' }}>Process</option>
                    <option value="shipped" {{ request()->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ request()->status === 'completed' ? 'selected' : '' }}>Completed
                    </option>
                    <option value="canceled" {{ request()->status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
                <select name="payment_status" class="border px-3 py-2 rounded">
                    <option value="">Filter by Payment Status</option>
                    <option value="paid" {{ request()->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request()->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid
                    </option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </div>
        </form>

        <!-- Transaction Table -->
        <table class="table-auto w-full mb-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2">Transaction ID</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Payment Status</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="border px-4 py-2">{{ $transaction->id }}</td>
                        <td class="border px-4 py-2">{{ $transaction->user->name }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($transaction->status) }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($transaction->payment_status) }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('transaction.show', $transaction->id) }}"
                                class="bg-gray-500 text-white px-2 py-1 rounded">View</a>
                            <a href="#" data-id="{{ $transaction->id }}"
                                class="bg-red-500 text-white px-2 py-1 rounded"
                                onclick="openDeleteModal({{ $transaction->id }})">Delete</a>
                            <a href="{{ route('transaction.cancel', $transaction->id) }}"
                                class="bg-red-500 text-white px-2 py-1 rounded">Cancel</a>
                            <!-- Actions berdasarkan status -->
                            @if ($transaction->payment_status === 'unpaid')
                                <a href="{{ route('transaction.accept', $transaction->id) }}"
                                    class="bg-green-500 text-white px-2 py-1 rounded">Accept Payment</a>
                            @elseif($transaction->status === 'process')
                                <a href="{{ route('transaction.ship', $transaction->id) }}"
                                    class="bg-blue-500 text-white px-2 py-1 rounded">Ship</a>
                            @elseif($transaction->status === 'shipped')
                                <a href="{{ route('transaction.complete', $transaction->id) }}"
                                    class="bg-indigo-500 text-white px-2 py-1 rounded">Complete</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }} <!-- Pagination -->
    </div>
    {{-- @include('admin.transactions.form-edit')
    
    @include('admin.transactions.show') --}}
    @include('admin.transactions.delete')
</x-layout-admin>
