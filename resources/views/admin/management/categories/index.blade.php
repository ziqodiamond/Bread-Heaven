{{-- resources/views/admin/management/categories/index.blade.php --}}
<x-layout-admin>
    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Manajemen Kategori</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua kategori produk</p>
        </div>

        {{-- ── Success Message ─────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Tabel ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Header dengan filter --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar Kategori</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $categories->total() }} kategori</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.management.categories.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama kategori..."
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        
                        <select name="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>

                        <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-700">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('admin.management.categories.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kategori
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Nama</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Slug</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Urutan</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">
                                    <code class="bg-gray-50 px-2 py-1 rounded text-[11px]">{{ $category->slug }}</code>
                                </td>
                                <td class="px-5 py-3.5 text-gray-500">
                                    {{ $category->sort_order }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $category->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.management.categories.edit', $category) }}"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.management.categories.destroy', $category) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">
                                    Belum ada kategori. Klik <strong>Tambah Kategori</strong> untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layout-admin>
