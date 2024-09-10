<!-- resources/views/admin/payment_methods/form-edit.blade.php -->
<form id="editForm{{ $method->id }}" action="{{ route('payment-methods.update', $method->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="edit_name{{ $method->id }}" class="block text-gray-700 dark:text-gray-200">Payment Method
            Name</label>
        <input type="text" id="edit_name{{ $method->id }}" name="nama"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            value="{{ $method->nama }}" required>
    </div>

    <div class="mb-4">
        <label for="edit_category{{ $method->id }}" class="block text-gray-700 dark:text-gray-200">Category</label>
        <input type="text" id="edit_category{{ $method->id }}" name="category"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            value="{{ $method->category }}" required>
    </div>

    <div class="mb-4">
        <label for="edit_fee{{ $method->id }}" class="block text-gray-700 dark:text-gray-200">Fee</label>
        <input type="number" id="edit_fee{{ $method->id }}" name="fee"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            value="{{ $method->fee }}" required>
    </div>

    <div class="mb-4">
        <label for="edit_status{{ $method->id }}" class="block text-gray-700 dark:text-gray-200">Status</label>
        <select id="edit_status{{ $method->id }}" name="status"
            class="block appearance-none w-full bg-white border border-gray-300 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="available" {{ $method->status === 'available' ? 'selected' : '' }}>Available</option>
            <option value="not available" {{ $method->status === 'not available' ? 'selected' : '' }}>Not
                Available</option>
        </select>
    </div>

    <div class="text-right">
        <button type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Save
        </button>
    </div>
</form>
