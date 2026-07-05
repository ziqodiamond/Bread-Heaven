{{-- resources/views/admin/management/discounts/index.blade.php --}}
<x-layout-admin>
    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Manajemen Diskon Produk</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola diskon untuk semua produk yang tersedia</p>
        </div>

        {{-- ── Tabel ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Header dengan filter --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar Diskon Produk</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $products->total() }} produk dengan diskon</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.management.discounts.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari produk..."
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        
                        <select name="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="not_available" {{ request('status') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>

                        <label class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
                            <input type="checkbox" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }} class="h-4 w-4">
                            <span class="text-gray-600">Aktif Saja</span>
                        </label>

                        <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-700">
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                <input type="checkbox" class="h-4 w-4" id="selectAll">
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Produk</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Harga Normal</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Harga Diskon</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Label</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Jadwal</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <input type="checkbox" class="product-checkbox h-4 w-4" value="{{ $product->id }}">
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" 
                                            class="h-8 w-8 rounded object-cover">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($product->sale_price)
                                        <span class="text-red-600 font-medium">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($product->discount_label)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                            {{ $product->discount_label }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($product->has_active_discount)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    @if ($product->discount_start_at && $product->discount_end_at)
                                        <div>
                                            <p class="font-medium">{{ $product->discount_start_at->format('d M Y') }}</p>
                                            <p>s.d {{ $product->discount_end_at->format('d M Y') }}</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Tanpa jadwal</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.management.discounts.edit', $product) }}"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.management.discounts.destroy', $product) }}" method="POST" 
                                            onsubmit="return confirm('Hapus diskon untuk produk ini?')" class="inline">
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
                                <td colspan="8" class="px-5 py-8 text-center text-gray-500">
                                    <p class="text-sm">Tidak ada produk dengan diskon</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        </div>

    </div>
</x-layout-admin>
