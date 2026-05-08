<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-5 rounded-lg shadow-lg w-1/2">
        <h3 class="text-lg font-bold mb-4">Edit Transaction</h3>
        <form method="POST" action="{{ route('admin.transactions.update', $transaction->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="status" class="block font-bold">Status:</label>
                <select name="status" id="status" class="border px-3 py-2 rounded w-full">
                    <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="process" {{ $transaction->status == 'process' ? 'selected' : '' }}>Process</option>
                    <option value="shipped" {{ $transaction->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ $transaction->status == 'completed' ? 'selected' : '' }}>Completed
                    </option>
                    <option value="canceled" {{ $transaction->status == 'canceled' ? 'selected' : '' }}>Canceled
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label for="payment_status" class="block font-bold">Payment Status:</label>
                <select name="payment_status" id="payment_status" class="border px-3 py-2 rounded w-full">
                    <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $transaction->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid
                    </option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded"
                onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>
