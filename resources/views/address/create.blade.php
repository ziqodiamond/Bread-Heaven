{{-- resources/views/address/create.blade.php --}}

<x-layout>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        input:focus,
        textarea:focus,
        select:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .leaflet-container {
            z-index: 1;
            font-family: inherit;
        }

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

        .coord-badge {
            font-variant-numeric: tabular-nums;
        }

        input[readonly] {
            cursor: default;
        }

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

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #111827;
        }
    </style>

    <form action="{{ route('address.store') }}" method="POST" x-data="{}" class="m-10">

        @csrf

        <div class="space-y-6 text-gray-900">

            {{-- ─── Header ─── --}}
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('address.index') }}"
                            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Alamat Saya
                        </a>
                        <span class="text-gray-300">/</span>
                        <span class="text-xs text-gray-500">Tambah Baru</span>
                    </div>

                    <h1 class="mt-2 text-xl font-semibold text-gray-900">Tambah Alamat</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Isi detail alamat pengiriman kamu</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('address.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Alamat
                    </button>
                </div>
            </div>

            {{-- ─── Section 1: Informasi Penerima ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">1</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Informasi Penerima</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Nama dan nomor telepon penerima paket</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- Nama penerima --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Nama Penerima <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="receiver_name" value="{{ old('receiver_name') }}"
                                placeholder="Contoh: Budi Santoso"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors
                                       {{ $errors->has('receiver_name') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('receiver_name')
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

                        {{-- Nomor telepon --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Nomor Telepon <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="receiver_phone" value="{{ old('receiver_phone') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 transition-colors
                                       {{ $errors->has('receiver_phone') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('receiver_phone')
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

                    </div>
                </div>
            </div>

            {{-- ─── Section 2: Wilayah & Alamat ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Detail Alamat</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Wilayah dan alamat lengkap pengiriman</p>
                    </div>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Provinsi & Kota --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Provinsi <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="province" value="{{ old('province') }}" placeholder="Jawa Barat"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm placeholder-gray-400 transition-colors
                                       {{ $errors->has('province') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('province')
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

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Kota / Kabupaten <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="Kota Bandung"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm placeholder-gray-400 transition-colors
                                       {{ $errors->has('city') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('city')
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
                    </div>

                    {{-- Kecamatan & Kode Pos --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Kecamatan <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="district" value="{{ old('district') }}"
                                placeholder="Coblong"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm placeholder-gray-400 transition-colors
                                       {{ $errors->has('district') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('district')
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

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                                Kode Pos <span class="text-red-400 normal-case tracking-normal">*</span>
                            </label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                placeholder="40135"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm placeholder-gray-400 transition-colors
                                       {{ $errors->has('postal_code') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">
                            @error('postal_code')
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
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                            Alamat Lengkap <span class="text-red-400 normal-case tracking-normal">*</span>
                        </label>
                        <textarea name="full_address" rows="3" placeholder="Jl. Contoh No. 123, RT 01/RW 02, Kelurahan Lebak Gede..."
                            class="w-full rounded-lg border px-3 py-2.5 text-sm placeholder-gray-400 transition-colors resize-none
                                   {{ $errors->has('full_address') ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-400' }}">{{ old('full_address') }}</textarea>
                        @error('full_address')
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

                    {{-- Catatan --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 uppercase tracking-wide">
                            Catatan <span
                                class="text-gray-400 normal-case tracking-normal font-normal">(opsional)</span>
                        </label>
                        <textarea name="notes" rows="2"
                            placeholder="Contoh: Rumah warna biru, pagar hitam. Kalau tidak ada orang, titip ke tetangga sebelah."
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ─── Section 3: Lokasi Peta ─── --}}
            <div class="rounded-xl border border-gray-100 bg-white overflow-hidden">

                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
                    <span class="section-number">3</span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Lokasi GPS</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Klik peta untuk pin lokasi yang akurat</p>
                    </div>
                    {{-- Badge opsional --}}
                    <span class="ml-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-500">Opsional</span>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Search bar --}}
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="map-search" placeholder="Cari lokasi atau alamat..."
                                class="w-full rounded-lg border border-gray-200 py-2.5 pl-9 pr-3 text-sm placeholder-gray-400 focus:border-gray-400 transition-colors">
                        </div>

                        <button type="button" id="btn-search-map"
                            class="flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            Cari
                        </button>

                        <button type="button" id="btn-my-location" title="Gunakan lokasi saat ini"
                            class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
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
                        <div id="map" class="h-[380px] w-full"></div>

                        <div id="map-hint"
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs text-gray-600 shadow-sm border border-gray-100 pointer-events-none transition-opacity duration-300">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Klik pada peta untuk menentukan lokasi alamat
                            </span>
                        </div>
                    </div>

                    {{-- Koordinat --}}
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

            {{-- ─── Section 4: Pengaturan ─── --}}
            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">

                {{-- Header --}}
                <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">

                    <span class="section-number">
                        4
                    </span>

                    <div>

                        <h2 class="text-sm font-semibold text-gray-900">
                            Pengaturan
                        </h2>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Konfigurasi alamat ini
                        </p>

                    </div>

                </div>



                {{-- Content --}}
                <div class="p-5">

                    <label
                        class="group flex cursor-pointer items-center justify-between rounded-lg px-4 py-3.5 transition-colors hover:bg-gray-50">

                        {{-- Left --}}
                        <div class="flex items-center gap-3">

                            {{-- Icon --}}
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 transition-colors group-hover:bg-gray-200">

                                <svg class="h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">

                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />

                                </svg>

                            </div>



                            {{-- Text --}}
                            <div>

                                <p class="text-sm font-medium text-gray-800">
                                    Jadikan Alamat Utama
                                </p>

                                <p class="text-xs text-gray-500">
                                    Alamat ini akan digunakan sebagai default saat checkout
                                </p>

                            </div>

                        </div>



                        {{-- Toggle --}}
                        <div class="relative">

                            <input type="checkbox" id="is_default" name="is_default" value="1"
                                class="peer sr-only" {{ old('is_default') ? 'checked' : '' }}>

                            <div
                                class="h-6 w-11 rounded-full bg-gray-200 transition-colors
                           peer-checked:bg-blue-500
                           after:absolute after:left-[2px] after:top-[2px]
                           after:h-5 after:w-5 after:rounded-full
                           after:bg-white after:transition-all
                           peer-checked:after:translate-x-5">
                            </div>

                        </div>

                    </label>

                </div>

            </div>
            {{-- ─── Action Bottom ─── --}}
            <div class="flex items-center justify-end gap-3 pb-6">
                <a href="{{ route('address.index') }}"
                    class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Alamat
                </button>
            </div>

        </div>

    </form>

    {{-- ─── Leaflet JS ─── --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ─── Default: Bandung ───
            const DEFAULT_LAT = -6.9175;
            const DEFAULT_LNG = 107.6191;
            const DEFAULT_ZOOM = 13;

            const map = L.map('map', {
                center: [DEFAULT_LAT, DEFAULT_LNG],
                zoom: DEFAULT_ZOOM,
            });

            map.zoomControl.setPosition('bottomright');

            // ─── Tile layers ───
            const googleHybrid = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            const googleSatellite = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            const googleRoad = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps',
                    maxZoom: 20,
                }
            );

            googleHybrid.addTo(map);

            L.control.layers({
                '🛰 Satelit + Jalan': googleHybrid,
                '🌍 Satelit': googleSatellite,
                '🗺 Peta Jalan': googleRoad,
            }, {}, {
                position: 'topright',
                collapsed: false,
            }).addTo(map);

            // ─── Custom marker icon ───
            const customIcon = L.divIcon({
                className: '',
                html: `
                    <div style="
                        width: 32px; height: 32px;
                        background: #111827;
                        border: 3px solid white;
                        border-radius: 50% 50% 50% 0;
                        transform: rotate(-45deg);
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        position: relative;
                    ">
                        <div style="
                            width: 10px; height: 10px;
                            background: white; border-radius: 50%;
                            position: absolute; top: 50%; left: 50%;
                            transform: translate(-50%, -50%);
                        "></div>
                    </div>
                `,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -36],
            });

            let marker = null;
            let currentLat = null;
            let currentLng = null;

            const inputLat = document.getElementById('latitude');
            const inputLng = document.getElementById('longitude');
            const latInd = document.getElementById('lat-indicator');
            const lngInd = document.getElementById('lng-indicator');
            const mapHint = document.getElementById('map-hint');

            // Restore dari old() jika ada validasi gagal
            if (inputLat.value && inputLng.value) {
                placeMarker(parseFloat(inputLat.value), parseFloat(inputLng.value));
            }

            function placeMarker(lat, lng) {
                currentLat = lat;
                currentLng = lng;

                inputLat.value = lat.toFixed(7);
                inputLng.value = lng.toFixed(7);

                latInd.classList.remove('hidden');
                lngInd.classList.remove('hidden');
                mapHint.style.opacity = '0';

                if (marker) map.removeLayer(marker);

                marker = L.marker([lat, lng], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-size:12px; font-family:inherit; min-width:160px;">
                            <div style="font-weight:600; color:#111827; margin-bottom:4px;">📍 Lokasi Saya</div>
                            <div style="color:#6b7280; font-size:11px;">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                            <button onclick="centerToMarker()"
                                style="margin-top:8px; font-size:11px; color:#3b82f6; cursor:pointer; background:none; border:none; padding:0; text-decoration:underline;">
                                Pusatkan peta
                            </button>
                        </div>
                    `);
            }

            map.on('click', (e) => {
                placeMarker(e.latlng.lat, e.latlng.lng);
            });

            window.centerToMarker = function() {
                if (currentLat !== null) {
                    map.flyTo([currentLat, currentLng], 16, {
                        animate: true,
                        duration: 1.2
                    });
                }
            };

            // ─── Tombol lokasi saya ───
            const btnLoc = document.getElementById('btn-my-location');
            btnLoc.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser kamu tidak mendukung geolocation.');
                    return;
                }
                this.disabled = true;
                this.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>`;
                const btn = this;
                const iconHtml =
                    `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`;
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        map.flyTo([pos.coords.latitude, pos.coords.longitude], 16, {
                            duration: 1.2
                        });
                        placeMarker(pos.coords.latitude, pos.coords.longitude);
                        btn.disabled = false;
                        btn.innerHTML = iconHtml;
                    },
                    () => {
                        alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.');
                        btn.disabled = false;
                        btn.innerHTML = iconHtml;
                    }
                );
            });

            // ─── Search Nominatim ───
            function searchLocation(query) {
                if (!query.trim()) return;
                fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`
                    )
                    .then(r => r.json())
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
                    .catch(() => alert('Gagal mencari lokasi.'));
            }

            document.getElementById('btn-search-map').addEventListener('click', () => {
                searchLocation(document.getElementById('map-search').value);
            });

            document.getElementById('map-search').addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation(e.target.value);
                }
            });

        });
    </script>

</x-layout>
