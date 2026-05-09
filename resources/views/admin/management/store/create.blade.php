{{-- resources/views/admin/management/store/create.blade.php --}}

<x-layout-admin>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Hilangkan fokus ring bawaan browser */
        input:focus,
        textarea:focus,
        select:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Leaflet map z-index fix di dalam card */
        .leaflet-container {
            z-index: 1;
            font-family: inherit;
        }

        /* Tombol center map custom */
        .leaflet-control-center {
            background: white;
            border: none !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12) !important;
        }

        .leaflet-control-center a {
            width: 32px !important;
            height: 32px !important;
            line-height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            color: #374151 !important;
            font-size: 16px !important;
            text-decoration: none !important;
            transition: background 0.15s ease;
        }

        .leaflet-control-center a:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        /* Animasi marker saat pertama muncul */
        @keyframes markerDrop {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Pulse ring di sekitar marker */
        .marker-pulse {
            position: relative;
        }

        /* Koordinat badge */
        .coord-badge {
            font-variant-numeric: tabular-nums;
        }

        /* Input read-only styling */
        input[readonly] {
            cursor: default;
        }

        /* File input styling */
        input[type="file"]::file-selector-button {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
            margin-right: 8px;
            transition: background 0.15s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #f3f4f6;
        }

        /* Section number badge */
        .section-number {
            width: 22px;
            height: 22px;
            background: #111827;
            color: white;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Checkbox custom */
        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #111827;
        }

        /* Layer control styling */
        .leaflet-control-layers {
            border: none !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15) !important;
            font-size: 12px !important;
            font-family: inherit !important;
        }

        .leaflet-control-layers-expanded {
            padding: 8px 12px !important;
        }

        .leaflet-control-layers label {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 3px 0 !important;
            cursor: pointer !important;
            color: #374151 !important;
            font-size: 12px !important;
        }

        .leaflet-control-layers-selector {
            accent-color: #111827 !important;
        }
    </style>

    <form action="{{ route('admin.management.stores.store') }}" method="POST" enctype="multipart/form-data"
        x-data="{ logoPreview: null, bannerPreview: null }">

        @csrf

        <div class="space-y-6 text-gray-900">

            {{-- ─── Header ─── --}}
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.management.stores.index') }}"
                            class="flex items-center gap-1.5 text-xs text-gray-600 hover:text-gray-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Manajemen Toko
                        </a>
                        <span class="text-gray-400">/</span>
                        <span class="text-xs text-gray-600">Tambah Baru</span>
                    </div>

                    <h1 class="mt-2 text-xl font-semibold text-gray-900">
                        Tambah Toko
                    </h1>

                    <p class="mt-0.5 text-sm text-gray-600">
                        Tambahkan toko baru untuk pickup dan origin shipping
                    </p>
                </div>

                {{-- Action di header untuk akses cepat --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.management.stores.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        Batal
                    </a>

                    <button type="submit"
                        class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Toko
                    </button>
                </div>
            </div>



            {{-- ─── Section 1: Informasi Dasar ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                {{-- Section header --}}
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">1</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Informasi Toko</h2>
                        <p class="text-xs text-gray-600 mt-0.5">Nama, kontak, dan alamat toko</p>
                    </div>
                </div>

                <div class="p-5 space-y-5">

                    {{-- Nama toko (full width) --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                            Nama Toko <span class="text-red-400 normal-case tracking-normal">*</span>
                        </label>

                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: Toko Utama Bandung"
                            class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors
                                   {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">

                        @error('name')
                            <p class="mt-1.5 flex items-center gap-1 text-xs text-red-500">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Grid 2 kolom: Email & Phone --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="toko@email.com"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Nomor Telepon
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="021-xxxxxxxx"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                        </div>

                    </div>

                    {{-- Whatsapp --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                WhatsApp
                            </label>
                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                </div>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                                    placeholder="628xxxxxxxxxx"
                                    class="w-full rounded-lg border border-gray-200 py-2.5 pl-9 pr-3 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                            </div>
                        </div>

                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100 pt-1">
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wide mb-4">Alamat</p>

                        {{-- Provinsi & Kota --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                    Provinsi
                                </label>
                                <input type="text" name="province" value="{{ old('province') }}"
                                    placeholder="Jawa Barat"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                    Kota / Kabupaten
                                </label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                    placeholder="Kota Bandung"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                            </div>
                        </div>

                        {{-- Kecamatan & Kode Pos --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mt-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                    Kecamatan
                                </label>
                                <input type="text" name="district" value="{{ old('district') }}"
                                    placeholder="Cimahi Utara"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                    Kode Pos
                                </label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                    placeholder="40511"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                            </div>
                        </div>

                        {{-- Alamat lengkap --}}
                        <div class="mt-4">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Alamat Lengkap
                            </label>
                            <textarea name="full_address" rows="3" placeholder="Jl. Contoh No. 123, RT 01/RW 02, Kelurahan..."
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors resize-none">{{ old('full_address') }}</textarea>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mt-4">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Deskripsi Toko
                            </label>
                            <textarea name="description" rows="3" placeholder="Deskripsi singkat tentang toko ini..."
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors resize-none">{{ old('description') }}</textarea>
                        </div>

                    </div>

                </div>
            </div>



            {{-- ─── Section 2: Lokasi Peta ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                {{-- Section header --}}
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Lokasi Peta</h2>
                        <p class="text-xs text-gray-600 mt-0.5">Klik peta untuk pin lokasi, atau cari alamat</p>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Search bar untuk peta --}}
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="map-search" placeholder="Cari lokasi atau alamat..."
                                class="w-full rounded-lg border border-gray-200 py-2.5 pl-9 pr-3 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                        </div>

                        <button type="button" id="btn-search-map"
                            class="flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            Cari
                        </button>

                        <button type="button" id="btn-my-location" title="Gunakan lokasi saat ini"
                            class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Peta --}}
                    <div class="relative rounded-xl overflow-hidden border border-gray-200">
                        <div id="map" class="h-[420px] w-full"></div>

                        {{-- Instruksi overlay --}}
                        <div id="map-hint"
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs text-gray-600 shadow-sm border border-gray-100 pointer-events-none transition-opacity duration-300">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Klik pada peta untuk menentukan lokasi toko
                            </span>
                        </div>
                    </div>

                    {{-- Koordinat display --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Latitude
                            </label>
                            <div class="relative">
                                <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                                    readonly placeholder="Belum dipilih"
                                    class="coord-badge w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5 text-sm text-gray-600 placeholder-gray-400">
                                <div id="lat-indicator" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                    <div class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Longitude
                            </label>
                            <div class="relative">
                                <input type="text" id="longitude" name="longitude"
                                    value="{{ old('longitude') }}" readonly placeholder="Belum dipilih"
                                    class="coord-badge w-full rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5 text-sm text-gray-600 placeholder-gray-400">
                                <div id="lng-indicator" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                    <div class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>



            {{-- ─── Section 3: Branding ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">3</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Branding</h2>
                        <p class="text-xs text-gray-600 mt-0.5">Logo dan banner toko</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        {{-- Logo --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Logo Toko
                            </label>

                            {{-- Preview area --}}
                            <div x-show="logoPreview"
                                class="mb-3 flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <img :src="logoPreview" alt="Preview"
                                    class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-600 truncate"
                                        x-text="logoPreview ? 'Preview logo' : ''">
                                    </p>
                                    <button type="button" @click="logoPreview = null; $refs.logoInput.value = ''"
                                        class="text-xs text-red-400 hover:text-red-600 mt-0.5">
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <label
                                class="flex flex-col items-center justify-center w-full h-28 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 cursor-pointer hover:bg-gray-100 hover:border-gray-300 transition-all"
                                x-show="!logoPreview">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs text-gray-600">Klik untuk upload logo</p>
                                    <p class="text-xs text-gray-400">PNG, JPG — Max 2MB</p>
                                </div>
                                <input type="file" name="logo" accept="image/*" x-ref="logoInput"
                                    class="hidden"
                                    @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => logoPreview = e.target.result; r.readAsDataURL(f); }">
                            </label>
                        </div>

                        {{-- Banner --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Banner Toko
                            </label>

                            <div x-show="bannerPreview"
                                class="mb-3 rounded-lg overflow-hidden border border-gray-100 relative">
                                <img :src="bannerPreview" alt="Preview" class="h-28 w-full object-cover">
                                <button type="button" @click="bannerPreview = null; $refs.bannerInput.value = ''"
                                    class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm rounded-md px-2 py-1 text-xs text-red-400 hover:text-red-600 border border-gray-100">
                                    Hapus
                                </button>
                            </div>

                            <label
                                class="flex flex-col items-center justify-center w-full h-28 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 cursor-pointer hover:bg-gray-100 hover:border-gray-300 transition-all"
                                x-show="!bannerPreview">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs text-gray-600">Klik untuk upload banner</p>
                                    <p class="text-xs text-gray-400">PNG, JPG — Max 4MB</p>
                                </div>
                                <input type="file" name="banner" accept="image/*" x-ref="bannerInput"
                                    class="hidden"
                                    @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => bannerPreview = e.target.result; r.readAsDataURL(f); }">
                            </label>
                        </div>

                    </div>
                </div>
            </div>



            {{-- ─── Section 4: Status & Konfigurasi ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">4</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Status & Konfigurasi</h2>
                        <p class="text-xs text-gray-600 mt-0.5">Pengaturan operasional toko</p>
                    </div>
                </div>

                <div class="p-5 space-y-1">

                    {{-- Toggle: Allow Pickup --}}
                    <label
                        class="flex items-center justify-between rounded-lg px-4 py-3.5 hover:bg-gray-100 cursor-pointer transition-colors group">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Aktifkan Pickup</p>
                                <p class="text-xs text-gray-600">Pembeli dapat mengambil pesanan di toko ini</p>
                            </div>
                        </div>
                        <input type="checkbox" name="allow_pickup" value="1"
                            {{ old('allow_pickup') ? 'checked' : '' }} class="rounded border-gray-300">
                    </label>

                    {{-- Toggle: Shipping Origin --}}
                    <label
                        class="flex items-center justify-between rounded-lg px-4 py-3.5 hover:bg-gray-100 cursor-pointer transition-colors group">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Origin Shipping Biteship</p>
                                <p class="text-xs text-gray-600">Jadikan toko ini sebagai titik asal pengiriman</p>
                            </div>
                        </div>
                        <input type="checkbox" name="is_shipping_origin" value="1"
                            {{ old('is_shipping_origin') ? 'checked' : '' }} class="rounded border-gray-300">
                    </label>

                    {{-- Toggle: Active --}}
                    <label
                        class="flex items-center justify-between rounded-lg px-4 py-3.5 hover:bg-gray-100 cursor-pointer transition-colors group">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Status Aktif</p>
                                <p class="text-xs text-gray-600">Toko akan tampil dan dapat diakses</p>
                            </div>
                        </div>
                        <input type="checkbox" name="is_active" value="1" checked
                            class="rounded border-gray-300">
                    </label>

                </div>
            </div>



            {{-- ─── Action Bottom ─── --}}
            <div class="flex items-center justify-end gap-3 pb-6">

                <a href="{{ route('admin.management.stores.index') }}"
                    class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </a>

                <button type="submit"
                    class="flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Toko
                </button>

            </div>

        </div>

    </form>



    {{-- ─── Leaflet JS ─── --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ─── Koordinat default: Cimahi ───
            const DEFAULT_LAT = -6.8722;
            const DEFAULT_LNG = 107.5428;
            const DEFAULT_ZOOM = 13;

            // ─── Inisialisasi peta ───
            const map = L.map('map', {
                center: [DEFAULT_LAT, DEFAULT_LNG],
                zoom: DEFAULT_ZOOM,
                zoomControl: true, // kontrol zoom default (kiri atas)
            });

            // Geser zoom control ke kanan bawah agar tidak bertabrakan
            map.zoomControl.setPosition('bottomright');

            // ─── Tile layers ───
            // Google Maps Hybrid (satelit + label jalan) — tanpa API key
            const googleHybrid = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            // Google Maps Satelit murni
            const googleSatellite = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            // Google Maps Road (tampilan peta biasa seperti Google Maps)
            const googleRoad = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            // Default: Hybrid (paling informatif untuk lokasi toko)
            googleHybrid.addTo(map);

            // ─── Layer control (switcher pojok kanan atas) ───
            L.control.layers({
                '🛰 Satelit + Jalan': googleHybrid,
                '🌍 Satelit': googleSatellite,
                '🗺 Peta Jalan': googleRoad,
            }, {}, {
                position: 'topright',
                collapsed: false
            }).addTo(map);

            // ─── Custom icon marker ───
            const customIcon = L.divIcon({
                className: '',
                html: `
                    <div style="
                        width: 32px;
                        height: 32px;
                        background: #111827;
                        border: 3px solid white;
                        border-radius: 50% 50% 50% 0;
                        transform: rotate(-45deg);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        position: relative;
                    ">
                        <div style="
                            width: 10px;
                            height: 10px;
                            background: white;
                            border-radius: 50%;
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                        "></div>
                    </div>
                `,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -36],
            });

            // ─── State marker & koordinat ───
            let marker = null;
            let currentLat = null;
            let currentLng = null;

            // ─── Referensi input & elemen UI ───
            const inputLat = document.getElementById('latitude');
            const inputLng = document.getElementById('longitude');
            const latInd = document.getElementById('lat-indicator');
            const lngInd = document.getElementById('lng-indicator');
            const mapHint = document.getElementById('map-hint');

            // Jika ada nilai old() dari Laravel, restore marker
            if (inputLat.value && inputLng.value) {
                const lat = parseFloat(inputLat.value);
                const lng = parseFloat(inputLng.value);
                placeMarker(lat, lng);
            }

            // ─── Fungsi: Taruh / pindahkan marker ───
            function placeMarker(lat, lng) {
                currentLat = lat;
                currentLng = lng;

                // Update input
                inputLat.value = lat.toFixed(7);
                inputLng.value = lng.toFixed(7);

                // Tampilkan indikator hijau
                latInd.classList.remove('hidden');
                lngInd.classList.remove('hidden');

                // Sembunyikan hint
                mapHint.style.opacity = '0';

                // Hapus marker lama, buat baru
                if (marker) map.removeLayer(marker);

                marker = L.marker([lat, lng], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-size:12px; font-family: inherit; min-width: 160px;">
                            <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">📍 Lokasi Toko</div>
                            <div style="color: #6b7280; font-size: 11px;">
                                ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            </div>
                            <button onclick="centerToMarker()"
                                style="margin-top: 8px; font-size: 11px; color: #3b82f6; cursor: pointer; background: none; border: none; padding: 0; text-decoration: underline;">
                                Pusatkan peta
                            </button>
                        </div>
                    `);
            }

            // ─── Klik peta: taruh marker ───
            map.on('click', function(e) {
                placeMarker(e.latlng.lat, e.latlng.lng);
            });

            // ─── Fungsi global: pusatkan peta ke marker (dipanggil dari popup) ───
            window.centerToMarker = function() {
                if (currentLat !== null && currentLng !== null) {
                    map.flyTo([currentLat, currentLng], 16, {
                        animate: true,
                        duration: 1.2,
                    });
                }
            };

            // ─── Tombol "Lokasi Saya" ───
            document.getElementById('btn-my-location').addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser kamu tidak mendukung geolocation.');
                    return;
                }

                this.disabled = true;
                this.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            class="opacity-25"></circle>
                        <path fill="currentColor" class="opacity-75"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                `;

                const btn = this;

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;

                        map.flyTo([lat, lng], 16, {
                            duration: 1.2
                        });
                        placeMarker(lat, lng);

                        btn.disabled = false;
                        btn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        `;
                    },
                    (err) => {
                        alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.');
                        btn.disabled = false;
                        btn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        `;
                    }
                );
            });

            // ─── Search Nominatim (OpenStreetMap geocoding) ───
            function searchLocation(query) {
                if (!query.trim()) return;

                fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`
                    )
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);

                            map.flyTo([lat, lng], 16, {
                                duration: 1.2
                            });
                            placeMarker(lat, lng);
                        } else {
                            alert('Lokasi tidak ditemukan. Coba kata kunci lain.');
                        }
                    })
                    .catch(() => {
                        alert('Gagal mencari lokasi. Periksa koneksi internet.');
                    });
            }

            // Tombol cari
            document.getElementById('btn-search-map').addEventListener('click', function() {
                const q = document.getElementById('map-search').value;
                searchLocation(q);
            });

            // Enter di input search
            document.getElementById('map-search').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Jangan submit form
                    searchLocation(this.value);
                }
            });

        });
    </script>

</x-layout-admin>
