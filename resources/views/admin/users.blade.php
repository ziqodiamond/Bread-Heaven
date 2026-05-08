<x-layout-admin>
    <div class="container mx-auto mt-5">
        <h2 class="text-xl font-bold mb-4">User Management</h2>

        <!-- Flash messages -->
        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Phone</th>
                    <th class="border px-4 py-2">Role</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->phone }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($user->role) }}</td>
                        <td class="border px-4 py-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>

                            <!-- Tombol Delete -->
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form>

                            <!-- Tombol Promote -->
                            @if ($user->role === 'user')
                                <form action="{{ route('admin.users.promote', $user->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded"
                                        onclick="return confirm('Promote to Admin?')">Promote</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout-admin>
