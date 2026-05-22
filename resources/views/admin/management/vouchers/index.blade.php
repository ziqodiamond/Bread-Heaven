{{-- resources/views/admin/management/vouchers/index.blade.php --}}
<x-layout-admin>
    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Manajemen Voucher</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua voucher dan kode diskon</p>
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
                    <p class="text-sm font-medium text-gray-900">Daftar Voucher</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $vouchers->total() }} voucher</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.management.vouchers.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kode atau nama..."
                            class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        
                        <select name="type" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                            <option value="">Semua Tipe</option>
                            <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                            <option value="percent" {{ request('type') === 'percent' ? 'selected' : '' }}>Percent</option>
                            <option value="free_shipping" {{ request('type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                        </select>

                        <select name="status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>

                        <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-700">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('admin.management.vouchers.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Voucher
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Kode</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Tipe</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Nilai</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Kuota / Terpakai</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Tanggal Kadaluarsa</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($vouchers as $voucher)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <code class="font-mono text-sm font-medium text-gray-900 bg-gray-50 px-2 py-1 rounded">
                                            {{ $voucher->code }}
                                        </code>
                                        @if ($voucher->description)
                                            <span class="text-xs text-gray-500">{{ $voucher->description }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $types = [
                                            'fixed' => ['badge' => 'bg-blue-50 text-blue-700', 'text' => 'Fixed Diskon'],
                                            'percent' => ['badge' => 'bg-purple-50 text-purple-700', 'text' => 'Persen'],
                                            'free_shipping' => ['badge' => 'bg-green-50 text-green-700', 'text' => 'Free Ongkir'],
                                        ];
                                        $type = $types[$voucher->type] ?? ['badge' => 'bg-gray-50 text-gray-700', 'text' => $voucher->type];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $type['badge'] }}">
                                        {{ $type['text'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm font-medium text-gray-900">
                                    @if ($voucher->type === 'percent')
                                        {{ $voucher->value }}%
                                    @elseif ($voucher->type === 'fixed')
                                        Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                    @else
                                        Gratis Ongkir
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-600">
                                    @php
                                        $usage = $voucher->usages->count();
                                        $quota = $voucher->quota;
                                        $remaining = $quota - $usage;
                                        $percentage = ($usage / $quota) * 100;
                                    @endphp
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium">{{ $usage }} / {{ $quota }}</span>
                                            <span class="text-xs text-gray-400">{{ round($percentage) }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gray-700 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-600">
                                    @if ($voucher->expired_at)
                                        <span>{{ $voucher->expired_at->format('d M Y') }}</span>
                                        <br>
                                        @if ($voucher->expired_at->isPast())
                                            <span class="text-red-600 font-medium">Sudah Kadaluarsa</span>
                                        @else
                                            <span class="text-gray-400">{{ $voucher->expired_at->diffForHumans() }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Tidak ada masa berlaku</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($voucher->is_active && !$voucher->expired_at?->isPast())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-600 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @elseif ($voucher->expired_at?->isPast())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Kadaluarsa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.management.vouchers.edit', $voucher) }}"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.management.vouchers.destroy', $voucher) }}" method="POST" 
                                            onsubmit="return confirm('Hapus voucher ini?')" class="inline">
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
                                    <p class="text-sm">Belum ada voucher</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $vouchers->links() }}
            </div>
        </div>

    </div>
</x-layout-admin>
