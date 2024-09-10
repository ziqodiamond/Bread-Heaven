<div>
    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 px-6 py-3">
        <div class="flex space-x-4">
            <!-- Filter by Name -->
            <div class="relative">
                <label for="name" class="sr-only">Filter by Name</label>
                <input type="text" id="name" name="name" value="{{ request('name') }}"
                    class="block appearance-none w-full bg-white border border-gray-300 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                    placeholder="Search by name">
            </div>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md">Create Product</button>
    </div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Product Name</th>
                <th scope="col" class="px-6 py-3">Description</th>
                <th scope="col" class="px-6 py-3">Price</th>
                <th scope="col" class="px-6 py-3">Stock</th>
                <th scope="col" class="px-6 py-3">Image</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $product->name }}</td>
                    <td class="px-6 py-4">{{ $product->description }}</td>
                    <td class="px-6 py-4">{{ $product->price }}</td>
                    <td class="px-6 py-4">{{ $product->stock }}</td>
                    <td class="px-6 py-4">
                        <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}"
                            class="h-10 w-10 rounded-full">
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="#"
                            onclick="openEditModal({{ $product->id }}, '{{ $product->name }}', '{{ $product->description }}', '{{ $product->price }}', '{{ $product->stock }}', '{{ $product->image_url }}')"
                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        <a href="#" onclick="openDeleteModal({{ $product->id }})"
                            class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
@foreach ($products as $product)
    <div id="editModal{{ $product->id }}"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-1/3">
            @include('admin.product.form-edit', ['product' => $product])
            <button onclick="closeEditModal({{ $product->id }})"
                class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md">Close</button>
        </div>
    </div>
@endforeach


<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-1/3">
        @include('admin.product.form-create')
        <button onclick="closeCreateModal()" class="bg-red-500 text-white px-4 py-2 rounded-md">Close</button>
    </div>
</div>

<script>
    function openEditModal(productId, name, description, price, stock, imageUrl) {
        const modal = document.getElementById(`editModal${productId}`);
        modal.classList.remove('hidden');

        document.getElementById(`edit_name${productId}`).value = name;
        document.getElementById(`edit_description${productId}`).value = description;
        document.getElementById(`edit_price${productId}`).value = price;
        document.getElementById(`edit_stock${productId}`).value = stock;
        document.getElementById(`current_image${productId}`).src = imageUrl; // Set image src for preview
    }

    function closeEditModal(productId) {
        const modal = document.getElementById(`editModal${productId}`);
        modal.classList.add('hidden');
    }

    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }
</script>
