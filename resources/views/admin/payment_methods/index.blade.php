<div>
    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 px-6 py-3">
        <div class="flex space-x-4">
            <!-- Filter by Category -->
            <div class="relative">
                <label for="category" class="sr-only">Filter by Category</label>
                <select id="category" name="category"
                    class="block appearance-none w-full bg-white border border-gray-300 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Status -->
            <div class="relative">
                <label for="status" class="sr-only">Filter by Status</label>
                <select id="status" name="status"
                    class="block appearance-none w-full bg-white border border-gray-300 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md">Create Payment
            Method</button>
    </div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Category</th>
                <th scope="col" class="px-6 py-3">Fee</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentMethods as $method)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $method->name }}</td>
                    <td class="px-6 py-4">{{ $method->category }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($method->fee, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $method->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $method->status === 'available' ? 'Available' : 'Not Available' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="#"
                            onclick="openEditModal({{ $method->id }}, '{{ $method->nama }}', '{{ $method->category }}', '{{ $method->fee }}', '{{ $method->icon }}', '{{ $method->status }}')"
                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        <a href="#" onclick="openDeleteModal({{ $method->id }})"
                            class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <!-- Include the form for creating a payment method -->
        @include('admin.payment_methods.form-create')
        <button onclick="closeCreateModal()" class="bg-red-500 text-white px-4 py-2 rounded-md">Close</button>
    </div>
</div>

<!-- Edit Modal -->
@foreach ($paymentMethods as $method)
    <div id="editModal{{ $method->id }}"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-1/3">
            <!-- Include the form for editing a payment method -->
            @include('admin.payment_methods.form-edit')
            <button onclick="closeEditModal({{ $method->id }})"
                class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md">Close</button>
        </div>
    </div>
@endforeach

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
        <p>Are you sure you want to delete this payment method?</p>
        <button id="confirmDeleteBtn" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete</button>
        <button onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(methodId, name, category, fee, icon, status) {
        const modal = document.getElementById(`editModal${methodId}`);
        modal.classList.remove('hidden');

        document.getElementById(`edit_name${methodId}`).value = name;
        document.getElementById(`edit_category${methodId}`).value = category;
        document.getElementById(`edit_fee${methodId}`).value = fee;
        document.getElementById(`edit_icon${methodId}`).src = icon;
        document.getElementById(`edit_status${methodId}`).value = status;
    }

    function closeEditModal(methodId) {
        document.getElementById(`editModal${methodId}`).classList.add('hidden');
    }

    function openDeleteModal(methodId) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('confirmDeleteBtn').setAttribute('data-method-id', methodId);
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const methodId = this.getAttribute('data-method-id');
        confirmDelete(methodId);
    });

    function confirmDelete(methodId) {
        console.log("Deleting method with URL: ", `/admin/payment_methods/${methodId}`);
        fetch(`/admin/payment_methods/${methodId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to delete the payment method.');
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
