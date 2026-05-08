<x-layout-admin>
    <div class="container mx-auto mt-5">
        <h2 class="text-xl font-bold mb-4">Edit User</h2>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-bold">Name:</label>
                <input type="text" name="name" id="name" class="border px-3 py-2 rounded w-full"
                    value="{{ old('name', $user->name) }}">
            </div>

            <div class="mb-4">
                <label for="email" class="block font-bold">Email:</label>
                <input type="email" name="email" id="email" class="border px-3 py-2 rounded w-full"
                    value="{{ old('email', $user->email) }}">
            </div>

            <div class="mb-4">
                <label for="phone" class="block font-bold">Phone:</label>
                <input type="text" name="phone" id="phone" class="border px-3 py-2 rounded w-full"
                    value="{{ old('phone', $user->phone) }}">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
        </form>
    </div>
</x-layout-admin>
