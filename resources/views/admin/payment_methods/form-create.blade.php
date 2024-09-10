<!-- form-input.blade.php -->
<form action="{{ route('payment-methods.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label for="nama" class="block text-gray-700 dark:text-gray-200">Payment Method Name</label>
        <input type="text" id="name" name="name"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            required>
    </div>

    <div class="mb-4">
        <label for="category" class="block text-gray-700 dark:text-gray-200">Category</label>
        <input type="text" id="category" name="category"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            required>
    </div>

    <div class="mb-4">
        <label for="fee" class="block text-gray-700 dark:text-gray-200">Fee</label>
        <input type="number" step="0.01" id="fee" name="fee"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            required>
    </div>


    <div class="mb-4">
        <label for="status" class="block text-gray-700 dark:text-gray-200">Status</label>
        <select id="status" name="status"
            class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            required>
            <option value="available">Available</option>
            <option value="not_available">Not Available</option>
        </select>
    </div>

    <div class="text-right">
        <button type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Save
        </button>
    </div>
</form>
