<x-layout-admin>
    <div class="space-y-5" x-data="paymentMethodManager()">

        {{-- Header halaman --}}
        <div>
            <h2 class="text-base font-medium text-gray-900">Payment Methods</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua metode pembayaran yang tersedia</p>
        </div>

        {{-- Card utama --}}
        <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

            {{-- Card header: filter + tombol create --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Daftar metode pembayaran</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $paymentMethods->count() }} metode terdaftar</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Filter Category --}}
                    <select name="category" onchange="this.form && this.form.submit()"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter Status --}}
                    <select name="status" onchange="this.form && this.form.submit()"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                        </option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>
                            Unavailable</option>
                    </select>

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
                                Metode</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Kode</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Kategori / Provider</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Nomor Rekening</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Fee</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Status</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-medium uppercase tracking-wider text-gray-400">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($paymentMethods as $method)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Logo + Nama --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if ($method->image_url)
                                            <img src="{{ $method->image }}" alt="{{ $method->name }}"
                                                class="h-8 w-8 rounded-lg object-contain border border-gray-100 bg-gray-50 p-1 shrink-0">
                                        @else
                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[10px] font-medium text-blue-700">
                                                {{ strtoupper(substr($method->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 leading-tight">{{ $method->name }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $method->account_name }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kode --}}
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-mono font-medium text-gray-600">
                                        {{ $method->code }}
                                    </span>
                                </td>

                                {{-- Kategori + Provider --}}
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-gray-700">{{ $method->category }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $method->provider ?? '-' }}</p>
                                </td>

                                {{-- Nomor Rekening --}}
                                <td class="px-5 py-3.5 text-xs font-mono text-gray-500">
                                    {{ $method->account_number ?? '-' }}
                                </td>

                                {{-- Fee — warna seragam semua pakai text-gray-700 --}}
                                <td class="px-5 py-3.5">
                                    @if ($method->fee_value > 0)
                                        <div class="space-y-0.5">
                                            {{-- Nilai fee --}}
                                            <p class="text-xs font-medium text-gray-700">
                                                @if ($method->fee_type === 'percent')
                                                    {{ rtrim(rtrim(number_format($method->fee_value, 2, '.', ''), '0'), '.') }}%
                                                @else
                                                    Rp {{ number_format($method->fee_value, 0, ',', '.') }}
                                                @endif
                                            </p>
                                            {{-- Label jenis + pajak --}}
                                            <p class="text-[11px] text-gray-400">
                                                {{ $method->fee_type === 'percent' ? 'Persentase' : 'Fixed' }}
                                                &bull;
                                                {{ $method->fee_tax_type === 'before_tax' ? 'Sebelum Pajak' : 'Setelah Pajak' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Gratis</span>
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
                                                'name' => $method->name,
                                                'code' => $method->code,
                                                'category' => $method->category,
                                                'provider' => $method->provider,
                                                'account_number' => $method->account_number,
                                                'account_name' => $method->account_name,
                                                'fee_type' => $method->fee_type,
                                                'fee_value' => $method->fee_value,
                                                'fee_tax_type' => $method->fee_tax_type,
                                                'image_url' => $method->image_url,
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
                                            action="{{ route('admin.management.payment-methods.destroy', $method->id) }}"
                                            method="POST" x-data
                                            @submit.prevent="if(confirm('Hapus metode pembayaran ini?')) $el.submit()">
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
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
                                    Belum ada metode pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================
             MODAL CREATE
             Modal: max-w-2xl (lebih lebar), max-h + overflow-y-auto agar scrollable
             ============================================================ --}}
        <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="background: rgba(0,0,0,0.35)">

            <div @click.outside="showCreate = false"
                class="w-full max-w-2xl rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden my-auto">

                {{-- Modal header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Tambah Metode Pembayaran</p>
                        <p class="text-xs text-gray-400 mt-0.5">Isi data metode pembayaran baru</p>
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

                {{-- Form --}}
                <form action="{{ route('admin.management.payment-methods.store') }}" method="POST"
                    enctype="multipart/form-data" class="px-6 py-5 space-y-5" x-data="{ createFeeType: 'fixed' }">
                    @csrf

                    {{-- Baris 1: Nama (full width) --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="cth. BCA Virtual Account"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    {{-- Baris 2: Kode + Kategori --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Kode <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" required placeholder="cth. BCA_VA"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Kategori <span
                                    class="text-red-500">*</span></label>
                            <select name="category" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Baris 3: Provider + Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Provider</label>
                            <input type="text" name="provider" placeholder="cth. Midtrans"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    {{-- Baris 4: Nomor Rekening + Nama Rekening --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Nomor Rekening</label>
                            <input type="text" name="account_number" placeholder="cth. 1234567890"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Nama Rekening</label>
                            <input type="text" name="account_name" placeholder="cth. PT. Toko Online"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>

                    {{-- Baris 5: Fee section — 3 kolom --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 space-y-3">
                        <p class="text-xs font-medium text-gray-600">Konfigurasi Fee</p>
                        <div class="grid grid-cols-3 gap-4">

                            {{-- Jenis Fee — reactive update step input --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Jenis Fee</label>
                                <select name="fee_type" x-model="createFeeType"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="fixed">Fixed</option>
                                    <option value="percent">Percent</option>
                                </select>
                            </div>

                            {{-- Nilai Fee — step integer kalau fixed, decimal kalau percent --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                    Nilai Fee
                                    <span class="text-gray-400 font-normal"
                                        x-text="createFeeType === 'percent' ? '(%)' : '(Rp)'"></span>
                                </label>
                                <input type="number" name="fee_value" value="0" min="0"
                                    :step="createFeeType === 'percent' ? '0.01' : '1'"
                                    :placeholder="createFeeType === 'percent' ? 'cth. 2.5' : 'cth. 5000'"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            </div>

                            {{-- Tipe Pajak --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Tipe Pajak</label>
                                <select name="fee_tax_type"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="after_tax">Setelah Pajak</option>
                                    <option value="before_tax">Sebelum Pajak</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Baris 6: Upload Logo --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700">Logo / Icon</label>
                        <input type="file" name="image_url" accept="image/*"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-blue-700 hover:file:bg-blue-100 focus:outline-none">
                    </div>

                    {{-- Footer modal --}}
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
        <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="background: rgba(0,0,0,0.35)">

            <div @click.outside="showEdit = false"
                class="w-full max-w-2xl rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden my-auto">

                {{-- Modal header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Edit Metode Pembayaran</p>
                        <p class="text-xs text-gray-400 mt-0.5" x-text="'Mengedit: ' + (editData.name ?? '')"></p>
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

                {{-- Form edit --}}
                <form :action="`{{ url('admin/payment-methods') }}/${editData.id}`" method="POST"
                    enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Baris 1: Nama --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" :value="editData.name" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    {{-- Baris 2: Kode + Kategori --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Kode <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" :value="editData.code" required
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Kategori <span
                                    class="text-red-500">*</span></label>
                            {{-- x-init $watch untuk sync value select --}}
                            <select name="category" required x-init="$watch('editData.category', v => $el.value = v)"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Baris 3: Provider + Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Provider</label>
                            <input type="text" name="provider" :value="editData.provider"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status" required x-init="$watch('editData.status', v => $el.value = v)"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    {{-- Baris 4: Nomor Rekening + Nama Rekening --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Nomor Rekening</label>
                            <input type="text" name="account_number" :value="editData.account_number"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-mono text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700">Nama Rekening</label>
                            <input type="text" name="account_name" :value="editData.account_name"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>

                    {{-- Baris 5: Fee section — reactive step berdasarkan fee_type --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 space-y-3">
                        <p class="text-xs font-medium text-gray-600">Konfigurasi Fee</p>
                        <div class="grid grid-cols-3 gap-4">

                            {{-- Jenis Fee --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Jenis Fee</label>
                                {{-- x-model ke editData.fee_type agar step input ikut berubah --}}
                                <select name="fee_type" x-model="editData.fee_type" x-init="$watch('editData.fee_type', v => $el.value = v)"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="fixed">Fixed</option>
                                    <option value="percent">Percent</option>
                                </select>
                            </div>

                            {{-- Nilai Fee — step reaktif --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                                    Nilai Fee
                                    <span class="text-gray-400 font-normal"
                                        x-text="editData.fee_type === 'percent' ? '(%)' : '(Rp)'"></span>
                                </label>
                                <input type="number" name="fee_value" :value="editData.fee_value" min="0"
                                    :step="editData.fee_type === 'percent' ? '0.01' : '1'"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            </div>

                            {{-- Tipe Pajak --}}
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700">Tipe Pajak</label>
                                <select name="fee_tax_type" x-init="$watch('editData.fee_tax_type', v => $el.value = v)"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="after_tax">Setelah Pajak</option>
                                    <option value="before_tax">Sebelum Pajak</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Baris 6: Preview + Upload Logo --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700">Logo / Icon</label>
                        <template x-if="editData.image_url">
                            <div class="mb-2 flex items-center gap-3">
                                <img :src="editData.image_url" alt="Logo saat ini"
                                    class="h-10 w-10 rounded-lg object-contain border border-gray-100 bg-gray-50 p-1">
                                <p class="text-xs text-gray-400">Logo saat ini. Upload baru untuk menggantinya.</p>
                            </div>
                        </template>
                        <input type="file" name="image_url" accept="image/*"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-blue-700 hover:file:bg-blue-100 focus:outline-none">
                    </div>

                    {{-- Footer modal --}}
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

    {{-- ================================================================
         Alpine.js component
         ================================================================ --}}
    <script>
        function paymentMethodManager() {
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
