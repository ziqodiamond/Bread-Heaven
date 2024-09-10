<!-- Create Modal -->

<h2 class="text-xl font-bold mb-4">Create Product</h2>
<form id="createForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="name" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>
    <div class="mb-4">
        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
        <input type="text" name="category" id="category" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>
    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
    </div>
    <div class="mb-4">
        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" name="price" id="price" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>
    <div class="mb-4">
        <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" name="stock" id="stock" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>
    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            <option value="available">Available</option>
            <option value="not available">Not Available</option>
        </select>
    </div>
    <div class="mb-4">
        <label for="image_url" class="block text-sm font-medium text-gray-700">Image</label>
        <input type="file" name="image_url" id="image_url" required class="mt-1 block w-full text-sm text-gray-500">
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Create</button>
</form>
