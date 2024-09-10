<!-- Edit Modal -->
<form id="editForm{{ $product->id }}" action="{{ route('products.update', $product->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="edit_name{{ $product->id }}" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="edit_name{{ $product->id }}" value="{{ $product->name }}" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label for="edit_category{{ $product->id }}" class="block text-sm font-medium text-gray-700">Category</label>
        <input type="text" name="category" id="edit_category{{ $product->id }}" value="{{ $product->category }}"
            required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label for="edit_description{{ $product->id }}"
            class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="edit_description{{ $product->id }}" rows="3" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $product->description }}</textarea>
    </div>

    <div class="mb-4">
        <label for="edit_price{{ $product->id }}" class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" name="price" id="edit_price{{ $product->id }}" value="{{ $product->price }}" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label for="edit_stock{{ $product->id }}" class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" name="stock" id="edit_stock{{ $product->id }}" value="{{ $product->stock }}" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label for="edit_status{{ $product->id }}" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="edit_status{{ $product->id }}" required
            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>Available</option>
            <option value="not available" {{ $product->status == 'not available' ? 'selected' : '' }}>Not Available
            </option>
        </select>
    </div>

    <div class="mb-4">
        <label for="edit_image_url{{ $product->id }}" class="block text-sm font-medium text-gray-700">Image</label>
        <input type="file" name="image_url" id="edit_image_url{{ $product->id }}"
            class="mt-1 block w-full text-sm text-gray-500">
        <img id="current_image{{ $product->id }}" src="{{ asset('storage/' . $product->image_url) }}"
            alt="{{ $product->name }}" class="mt-2 h-20 w-20 rounded-md">
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Update</button>
</form>
