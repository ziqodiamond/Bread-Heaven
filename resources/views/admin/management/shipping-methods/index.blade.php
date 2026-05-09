<x-layout-admin>
    <div class="space-y-5" x-data="shippingMethodManager()">

        {{-- Header halaman --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Shipping Methods</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua metode pengiriman yang tersedia</p>
        </div>



        {{-- Card utama --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Card header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar metode pengiriman</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $shippingMethods->count() }} metode terdaftar</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Filter Status --}}
                    <form method="GET" action="{{ route('admin.management.shipping-methods.index') }}" id="filterForm">
                        <select name="status" onchange="document.getElementById('filterForm').submit()"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                                Available</option>
                            <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>
                                Unavailable</option>
                        </select>
                    </form>

                    {{-- Tombol Create --}}
                    <button @click="openCreate()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Metode
                    </button>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Kurir</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Layanan</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Estimasi</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Biaya Tambahan</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Status</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($shippingMethods as $method)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Kurir: inisial + nama + kode --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-[10px] font-medium text-indigo-700">
                                            {{ strtoupper(substr($method->courier_code, 0, 3)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 leading-tight">
                                                {{ $method->courier_name }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">
                                                {{ $method->provider ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Layanan: nama + kode --}}
                                <td class="px-5 py-3.5">
                                    <p class="text-gray-700 text-xs font-medium">{{ $method->service_name }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span
                                            class="inline-flex items-center rounded-md bg-gray-100 px-1.5 py-0.5 text-[10px] font-mono font-medium text-gray-500">
                                            {{ $method->service_code }}
                                        </span>
                                        @if ($method->description)
                                            <span class="text-[11px] text-gray-400 truncate max-w-[140px]">
                                                {{ $method->description }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Estimasi --}}
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ $method->estimated_delivery ?? '-' }}
                                </td>

                                {{-- Biaya Tambahan --}}
                                <td class="px-5 py-3.5 text-xs text-gray-700">
                                    @if ($method->additional_fee > 0)
                                        Rp {{ number_format($method->additional_fee, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">Tidak ada</span>
                                    @endif
                                </td>

                                {{-- Badge Status --}}
                                <td class="px-5 py-3.5">
                                    @if ($method->status === 'available')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Available
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                                            Unavailable
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">

                                        {{-- Edit --}}
                                        <button
                                            @click="openEdit({{ json_encode([
                                                'id' => $method->id,
                                                'provider' => $method->provider,
                                                'courier_name' => $method->courier_name,
                                                'courier_code' => $method->courier_code,
                                                'service_name' => $method->service_name,
                                                'service_code' => $method->service_code,
                                                'description' => $method->description,
                                                'estimated_delivery' => $method->estimated_delivery,
                                                'additional_fee' => $method->additional_fee,
                                                'status' => $method->status,
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>

                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('admin.management.shipping-methods.destroy', $method->id) }}"
                                            method="POST" x-data
                                            @submit.prevent="if(confirm('Hapus metode pengiriman ini?')) $el.submit()">
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
                                                Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                    Belum ada metode pengiriman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================
             MODAL CREATE
             ============================================================ --}}
        <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.35)">

            <div @click.outside="showCreate = false"
                class="w-full max-w-lg rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">

                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Tambah Metode Pengiriman</p>
                        <p class="text-xs text-gray-400 mt-0.5">Isi data metode pengiriman baru</p>
                    </div>
                    <button @click="showCreate = false"
                        class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.management.shipping-methods.store') }}" method="POST"
                    class="px-5 py-4 space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">

                        {{-- Provider --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Provider</label>
                            <input type="text" name="provider" placeholder="cth. Biteship, Raja Ongkir"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Nama Kurir --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Nama Kurir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="courier_name" required placeholder="cth. JNE"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Kode Kurir --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Kode Kurir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="courier_code" required placeholder="cth. jne"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Nama Layanan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Nama Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="service_name" required placeholder="cth. Reguler"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Kode Layanan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Kode Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="service_code" required placeholder="cth. REG"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Deskripsi</label>
                            <input type="text" name="description" placeholder="cth. Pengiriman standar 2-3 hari"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Estimasi --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Estimasi Pengiriman</label>
                            <input type="text" name="estimated_delivery" placeholder="cth. 2-3 hari"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Biaya Tambahan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Biaya Tambahan (Rp)</label>
                            <input type="number" name="additional_fee" value="0" min="0"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Status --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showCreate = false"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================
             MODAL EDIT
             ============================================================ --}}
        <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.35)">

            <div @click.outside="showEdit = false"
                class="w-full max-w-lg rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">

                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Edit Metode Pengiriman</p>
                        <p class="text-xs text-gray-400 mt-0.5"
                            x-text="'Mengedit: ' + (editData.courier_name ?? '') + ' ' + (editData.service_name ?? '')">
                        </p>
                    </div>
                    <button @click="showEdit = false"
                        class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="`{{ url('admin/shipping-methods') }}/${editData.id}`" method="POST"
                    class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">

                        {{-- Provider --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Provider</label>
                            <input type="text" name="provider" :value="editData.provider"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Nama Kurir --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Nama Kurir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="courier_name" :value="editData.courier_name" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Kode Kurir --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Kode Kurir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="courier_code" :value="editData.courier_code" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Nama Layanan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Nama Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="service_name" :value="editData.service_name" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Kode Layanan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Kode Layanan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="service_code" :value="editData.service_code" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Deskripsi</label>
                            <input type="text" name="description" :value="editData.description"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Estimasi --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Estimasi Pengiriman</label>
                            <input type="text" name="estimated_delivery" :value="editData.estimated_delivery"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Biaya Tambahan --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Biaya Tambahan (Rp)</label>
                            <input type="number" name="additional_fee" :value="editData.additional_fee"
                                min="0"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        {{-- Status --}}
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required x-init="$watch('editData.status', v => $el.value = v)"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showEdit = false"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function shippingMethodManager() {
            return {
                showCreate: false,
                showEdit: false,
                editData: {},

                openCreate() {
                    this.showCreate = true;
                },

                openEdit(data) {
                    this.editData = data;
                    this.showEdit = true;
                },
            }
        }
    </script>
</x-layout-admin>
