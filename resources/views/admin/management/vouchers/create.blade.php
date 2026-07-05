{{-- resources/views/admin/management/vouchers/create.blade.php --}}
<x-layout-admin>
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.management.vouchers.index') }}"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-base font-medium text-gray-900">Buat Voucher Baru</h2>
                <p class="text-sm text-gray-400 mt-0.5">Tambahkan kode diskon baru untuk pelanggan</p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.management.vouchers.store') }}" method="POST"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf

            {{-- Kode & Deskripsi --}}
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-medium text-gray-600">Nama Voucher <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Promo Ramadhan"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="code" class="block text-xs font-medium text-gray-600">Kode Voucher <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                placeholder="Contoh: FLASH2024"
                                class="flex-1 rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 font-mono uppercase
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <button type="button" onclick="generateCode()"
                                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-medium text-gray-700 transition-colors">Generate</button>
                        </div>
                        @error('code')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">Deskripsi (Opsional)</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                        placeholder="Contoh: Flash Sale Akhir Bulan"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                               placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
            </div>

            {{-- Tipe & Nilai Diskon --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Tipe & Nilai Diskon</h3>
                
                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-medium text-gray-600">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required onchange="updateValueLabel()"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-0">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Diskon Tetap (Fixed)</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Diskon Persen (%)</option>
                        <option value="free_shipping" {{ old('type') === 'free_shipping' ? 'selected' : '' }}>Gratis Ongkir</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5" id="valueField">
                    <label for="value" class="block text-xs font-medium text-gray-600">
                        <span id="valueLabel">Nilai Voucher</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="value" id="value" value="{{ old('value', 0) }}" 
                            placeholder="0" min="0" step="1"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                   placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        <span id="valueSuffix" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium"></span>
                    </div>
                    @error('value')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Minimum Purchase (migration name: minimum_purchase) --}}
                <div class="space-y-1.5">
                    <label for="minimum_purchase" class="block text-xs font-medium text-gray-600">Pembelian Minimum (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="minimum_purchase" id="minimum_purchase" value="{{ old('minimum_purchase', 0) }}" placeholder="0" min="0"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pl-8 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    @error('minimum_purchase')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Maximum discount (for percent type) --}}
                <div class="space-y-1.5">
                    <label for="maximum_discount" class="block text-xs font-medium text-gray-600">Maksimal Potongan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="maximum_discount" id="maximum_discount" value="{{ old('maximum_discount') }}" placeholder="0" min="0"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pl-8 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>
                    @error('maximum_discount')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Kuota & Tanggal --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Kuota & Periode Voucher</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="quota" class="block text-xs font-medium text-gray-600">Kuota <span class="text-red-500">*</span></label>
                        <input type="number" name="quota" id="quota" value="{{ old('quota', 0) }}" required min="1" placeholder="Jumlah maksimal penggunaan"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        @error('quota')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="start_at" class="block text-xs font-medium text-gray-600">Mulai Pada (Opsional)</label>
                        <input type="text" name="start_at" id="start_at" value="{{ old('start_at') }}"
                            placeholder="Pilih tanggal dan jam" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 datetimepicker">
                        @error('start_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                        <label for="end_at" class="block text-xs font-medium text-gray-600 mt-2">Selesai Pada (Opsional)</label>
                        <input type="text" name="end_at" id="end_at" value="{{ old('end_at') }}"
                            placeholder="Pilih tanggal dan jam" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 datetimepicker">
                        @error('end_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Aktifkan voucher sekarang</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="max_usage_per_user" class="block text-xs font-medium text-gray-600">Limit per User (Jumlah penggunaan per user)</label>
                            <input type="number" name="max_usage_per_user" id="max_usage_per_user" value="{{ old('max_usage_per_user', 1) }}" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @error('max_usage_per_user')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_stackable" value="1" {{ old('is_stackable') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Boleh digabung dengan voucher lain (stackable)</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer mt-2">
                                <input type="checkbox" name="members_only" value="1" {{ old('members_only') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Hanya untuk pengguna terdaftar (members only)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tambahan meta & tampilan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Tampilan & SEO</h3>
                <div class="space-y-2">
                    <label for="label" class="block text-xs font-medium text-gray-600">Label Promo (opsional)</label>
                    <input type="text" name="label" id="label" value="{{ old('label') }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">

                    <label for="badge_color" class="block text-xs font-medium text-gray-600 mt-2">Warna Badge (opsional)</label>
                    <input type="text" name="badge_color" id="badge_color" value="{{ old('badge_color') }}" placeholder="red, blue, green" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">

                    <label for="meta_title" class="block text-xs font-medium text-gray-600 mt-2">Meta Title (opsional)</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">

                    <label for="meta_description" class="block text-xs font-medium text-gray-600 mt-2">Meta Description (opsional)</label>
                    <textarea name="meta_description" id="meta_description" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700" rows="3">{{ old('meta_description') }}</textarea>
                </div>
            </div>

            {{-- Info Note --}}
            <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs font-medium text-blue-900">💡 Tips:</p>
                <ul class="text-xs text-blue-700 mt-1 space-y-0.5">
                    <li>• Klik tombol "Generate" untuk membuat kode acak</li>
                    <li>• Kuota adalah jumlah maksimal penggunaan voucher</li>
                    <li>• Gunakan Maximum Discount untuk membatasi potongan pada voucher persen</li>
                    <li>• Gunakan Start / End untuk menjadwalkan periode voucher</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.management.vouchers.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>
                    Buat Voucher
                </button>
            </div>

        </form>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        function generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = code;
        }

        function updateValueLabel() {
            const type = document.getElementById('type').value;
            const label = document.getElementById('valueLabel');
            const suffix = document.getElementById('valueSuffix');
            const valueField = document.getElementById('valueField');

            if (type === 'free_shipping') {
                valueField.style.display = 'none';
            } else {
                valueField.style.display = 'block';
                if (type === 'percent') {
                    label.textContent = 'Diskon (%)';
                    suffix.textContent = '%';
                } else if (type === 'fixed') {
                    label.textContent = 'Diskon (Rp)';
                    suffix.textContent = '';
                }
            }
        }

        // Init on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateValueLabel();

            // Initialize flatpickr for start/end inputs
            try {
                flatpickr("#start_at", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    allowInput: true,
                    defaultDate: document.getElementById('start_at').value || null
                });

                flatpickr("#end_at", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    allowInput: true,
                    defaultDate: document.getElementById('end_at').value || null
                });
            } catch (e) {
                console.warn('flatpickr init failed', e);
            }
        });
    </script>
</x-layout-admin>
