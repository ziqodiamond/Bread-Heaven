<x-layout-admin>
    <div class="space-y-5">

        {{-- Header halaman --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">User Management</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua pengguna yang terdaftar</p>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2.5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Card header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar pengguna</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $users->count() }} pengguna terdaftar</p>
                </div>
            </div>

            {{-- Wrapper scroll horizontal untuk mobile --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Name</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Email</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Phone</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Role</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Avatar + nama --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-medium text-blue-700">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-gray-500">{{ $user->email }}</td>
                                <td class="px-5 py-3.5 text-gray-500">{{ $user->phone }}</td>

                                {{-- Badge role --}}
                                <td class="px-5 py-3.5">
                                    @if ($user->role === 'admin')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            Admin
                                        </span>
                                    @elseif($user->role === 'super_admin')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Super Admin
                                        </span>
                                    @elseif($user->role === 'user')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            User
                                        </span>
                                    @endif
                                </td>

                                {{-- Action buttons --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.management.users.edit', $user->id) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.management.users.destroy', $user->id) }}"
                                            method="POST" x-data
                                            @submit.prevent="if(confirm('Hapus pengguna ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>

                                        {{-- Promote — hanya tampil jika role = user --}}
                                        @if ($user->role === 'user')
                                            <form action="{{ route('admin.management.users.promote', $user->id) }}"
                                                method="POST" x-data
                                                @submit.prevent="if(confirm('Promote user ini ke Admin?')) $el.submit()">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-green-100 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                    </svg>
                                                    Promote
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layout-admin>
