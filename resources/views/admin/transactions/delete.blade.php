<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-1/3">
        <p class="text-lg font-semibold mb-4">Are you sure you want to delete this product?</p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete</button>
            <button type="button" onclick="closeDeleteModal()"
                class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
        </form>
    </div>
</div>
<script>
    function openDeleteModal(transactionId) {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/transactions/${transactionId}`; // Ganti route sesuai dengan route delete produk
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
