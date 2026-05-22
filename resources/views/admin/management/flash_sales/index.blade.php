{{-- resources/views/admin/management/flash_sales/index.blade.php --}}
<x-layout-admin>
    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Manajemen Flash Sale</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua flash sale dan produk dalamnya</p>
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
                    <p class="text-sm font-medium text-gray-900">Daftar Flash Sale</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $flashSales->total() }} flash sale</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.management.flash_sales.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari flash sale..."
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        
                        <select name="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                            <option value="">Semua Status</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>

                        <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-700">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('admin.management.flash_sales.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Flash Sale
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Nama</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Label</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Jadwal</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Items</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Statistik</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($flashSales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $sale->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $sale->slug }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium text-white"
                                        style="background-color: {{ $sale->badge_color ?? '#ef4444' }}">
                                        {{ $sale->label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-600">
                                    <div>
                                        <p class="font-medium">{{ $sale->start_at->format('d M Y H:i') }}</p>
                                        <p class="text-gray-400">s.d {{ $sale->end_at->format('d M Y H:i') }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                        {{ $sale->items->count() }} produk
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($sale->is_running)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-600 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @elseif ($sale->status === 'scheduled')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                            Terjadwal
                                        </span>
                                    @elseif ($sale->status === 'expired')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Expired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs">
                                    <div class="space-y-0.5">
                                        <p class="text-gray-600"><span class="font-medium">{{ $sale->total_views }}</span> views</p>
                                        <p class="text-gray-600"><span class="font-medium">{{ $sale->total_items_sold }}</span> terjual</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.management.flash_sales.edit', $sale) }}"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.management.flash_sales.destroy', $sale) }}" method="POST" 
                                            onsubmit="return confirm('Hapus flash sale ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                    <p class="text-sm">Belum ada flash sale</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $flashSales->links() }}
            </div>
        </div>

    </div>
</x-layout-admin>
