{{-- resources/views/components/navbar.blade.php --}}

@php
    $initials = '';
    if (Auth::check()) {
        $words = explode(' ', trim(Auth::user()->name));
        $initials = strtoupper(substr($words[0], 0, 1));
        if (count($words) > 1) {
            $initials .= strtoupper(substr(end($words), 0, 1));
        }
    }
@endphp

{{--
    PENTING: Pastikan layout wrapper menggunakan:
    - nav: position:relative; z-index:100
    - dropdown: z-index:9999 (via Tailwind: z-[9999])
    - <main>: TIDAK boleh punya isolation:isolate atau z-index lebih tinggi dari nav
--}}

{{-- ===== SIDEBAR MOBILE (Alpine.js) ===== --}}
<div x-data="{ sidebarOpen: false }">

    {{-- Overlay backdrop --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 z-[200] md:hidden" style="display:none">
    </div>

    {{-- Sidebar panel --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 h-full w-[270px] bg-[#0f3460] z-[300] md:hidden flex flex-col shadow-2xl"
        style="display:none">

        {{-- Sidebar header --}}
        <div class="flex items-center justify-between px-5 h-[62px] border-b border-white/10 shrink-0">
            {{-- Brand --}}
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-2 h-2 rounded-full bg-blue-400 group-hover:bg-blue-300 transition-colors"></div>
                <span class="text-[17px] font-semibold text-white tracking-tight">
                    Online<span class="text-blue-400">Store</span>
                </span>
            </a>
            {{-- Tombol tutup --}}
            <button @click="sidebarOpen = false"
                class="w-8 h-8 rounded-lg bg-white/10 border border-white/15
                       flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        {{-- Sidebar nav links --}}
        <nav class="flex-1 overflow-y-auto py-3 px-3">

            {{-- Jika auth: tampilkan info user di atas --}}
            @auth
                <div class="flex items-center gap-3 px-3 py-3 mb-2 rounded-xl bg-white/8 border border-white/10">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-700 border-2 border-white/30
                                flex items-center justify-center text-sm font-semibold text-white
                                overflow-hidden shrink-0">
                        @if (Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-[13.5px] font-semibold text-white truncate leading-tight">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-[11.5px] text-blue-300 truncate mt-0.5">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            @endauth

            {{-- Nav items --}}
            {{-- Beranda --}}
            <a href="/"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                       transition-colors
                       {{ request()->is('/') ? 'bg-blue-500/25 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 9.75L12 3l9 6.75V21a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z" />
                    <path d="M9 22V12h6v10" />
                </svg>
                Beranda
            </a>

            {{-- Produk --}}
            <a href="/products"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                       transition-colors
                       {{ request()->is('products') ? 'bg-blue-500/25 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="2" y="3" width="20" height="14" rx="2" />
                    <path d="M8 21h8M12 17v4" />
                </svg>
                Produk
            </a>

            @auth
                {{-- Riwayat Transaksi --}}
                <a href="{{ route('transaction.history') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                           transition-colors
                           {{ request()->routeIs('transaction.history') ? 'bg-blue-500/25 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                    Riwayat
                </a>

                {{-- Profil Saya --}}
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                           text-white/75 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Profil Saya
                </a>

                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
                    {{-- Dashboard Admin --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                               text-white/75 hover:bg-white/10 hover:text-white transition-colors">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        Dashboard Admin
                    </a>
                @endif
            @endauth

            @guest
                <div class="h-px bg-white/10 my-2"></div>
                <a href="{{ route('login') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                           text-white/75 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Masuk
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                               text-white/75 hover:bg-white/10 hover:text-white transition-colors">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" y1="8" x2="19" y2="14" />
                            <line x1="22" y1="11" x2="16" y2="11" />
                        </svg>
                        Daftar
                    </a>
                @endif
            @endguest
        </nav>

        {{-- Sidebar footer: tombol logout --}}
        @auth
            <div class="shrink-0 px-3 py-3 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium
                               text-red-300 hover:bg-red-500/15 hover:text-red-200 transition-colors">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        @endauth
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="relative z-[100] bg-[#0f3460]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-[62px] items-center justify-between">

                {{-- ===== DESKTOP: BRAND + NAV LINKS ===== --}}
                <div class="hidden md:flex items-center gap-10">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-2 h-2 rounded-full bg-blue-400 group-hover:bg-blue-300 transition-colors"></div>
                        <span class="text-[17px] font-semibold text-white tracking-tight">
                            Online<span class="text-blue-400">Store</span>
                        </span>
                    </a>

                    {{-- Nav links desktop — tanpa component --}}
                    <div class="flex items-center gap-0.5">

                        {{-- Beranda --}}
                        <a href="/"
                            class="px-3.5 py-1.5 rounded-lg text-[13.5px] font-medium transition-all duration-150
                                   {{ request()->is('/') ? 'bg-white/15 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                            Beranda
                        </a>

                        {{-- Produk --}}
                        <a href="/products"
                            class="px-3.5 py-1.5 rounded-lg text-[13.5px] font-medium transition-all duration-150
                                   {{ request()->is('products') ? 'bg-white/15 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                            Produk
                        </a>

                        @auth
                            {{-- Riwayat --}}
                            <a href="{{ route('transaction.history') }}"
                                class="px-3.5 py-1.5 rounded-lg text-[13.5px] font-medium transition-all duration-150
                                       {{ request()->routeIs('transaction.history') ? 'bg-white/15 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                                Riwayat
                            </a>
                        @endauth

                    </div>
                </div>

                {{-- ===== MOBILE: Hamburger (kiri) ===== --}}
                <div class="flex md:hidden">
                    <button @click="sidebarOpen = true"
                        class="w-9 h-9 rounded-lg bg-white/10 border border-white/15
                               flex items-center justify-center text-white hover:bg-white/18 transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <line x1="3" y1="12" x2="21" y2="12" />
                            <line x1="3" y1="18" x2="21" y2="18" />
                        </svg>
                    </button>
                </div>

                {{-- ===== MOBILE: Logo tengah ===== --}}
                <div class="absolute left-1/2 -translate-x-1/2 flex md:hidden">
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="w-2 h-2 rounded-full bg-blue-400 group-hover:bg-blue-300 transition-colors"></div>
                        <span class="text-[16px] font-semibold text-white tracking-tight">
                            Online<span class="text-blue-400">Store</span>
                        </span>
                    </a>
                </div>

                {{-- ===== DESKTOP: Action buttons (kanan) ===== --}}
                <div class="hidden md:flex items-center gap-2">

                    @auth
                        {{-- Tombol Cart --}}
                        <button @click="$dispatch('open-cart')" {{-- trigger buka modal --}}
                            class="relative w-9 h-9 rounded-lg flex items-center justify-center
           bg-white/10 border border-white/15 text-white/85
           hover:bg-white/18 hover:text-white transition-all duration-150"
                            title="Keranjang">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                                <line x1="3" y1="6" x2="21" y2="6" />
                                <path d="M16 10a4 4 0 01-8 0" />
                            </svg>
                            @if (Auth::user()->cart?->items->count() > 0)
                                <span
                                    class="absolute -top-1.5 -right-1.5 min-w-[17px] h-[17px] px-1
                     bg-blue-500 text-white text-[10px] font-bold rounded-full
                     flex items-center justify-center border-2 border-[#0f3460]">
                                    {{ Auth::user()->cart->items->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Tombol Notifikasi --}}
                        <button
                            class="w-9 h-9 rounded-lg flex items-center justify-center
                                       bg-white/10 border border-white/15 text-white/85
                                       hover:bg-white/18 hover:text-white transition-all duration-150"
                            title="Notifikasi">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                        </button>
                    @endauth

                    {{-- Separator --}}
                    <div class="w-px h-5 bg-white/15 mx-1"></div>

                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ isOpen: false }" @click.away="isOpen = false">

                        <button @click="isOpen = !isOpen"
                            class="w-[34px] h-[34px] rounded-full flex items-center justify-center
                                   bg-blue-700 border-2 border-white/35 font-semibold text-sm text-white
                                   hover:border-white/70 hover:scale-105 transition-all duration-150
                                   focus:outline-none focus:ring-2 focus:ring-white/40 overflow-hidden">
                            @auth
                                @if (Auth::user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                        class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                                @else
                                    {{ $initials }}
                                @endif
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            @endauth
                        </button>

                        <div x-show="isOpen" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 top-[calc(100%+10px)] w-[210px]
                                   bg-white rounded-xl overflow-hidden
                                   shadow-[0_20px_60px_rgba(0,0,0,0.18),0_4px_16px_rgba(0,0,0,0.1)]
                                   ring-1 ring-black/[0.06] z-[9999]"
                            role="menu">

                            @auth
                                {{-- Header dropdown --}}
                                <div class="flex items-center gap-2.5 px-4 py-3 bg-blue-50 border-b border-blue-100">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-700 border-2 border-blue-200
                                                flex items-center justify-center text-sm font-semibold text-white
                                                overflow-hidden shrink-0">
                                        @if (Auth::user()->profile_photo_path)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                                class="w-full h-full object-cover" alt="">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13.5px] font-semibold text-[#0f3460] truncate leading-tight">
                                            {{ Auth::user()->name }}
                                        </p>
                                        <p class="text-[11.5px] text-blue-500 truncate mt-0.5">
                                            {{ Auth::user()->email }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Menu items --}}
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" role="menuitem"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                               hover:bg-blue-50 hover:text-[#0f3460] transition-colors">
                                        <i class="ti ti-user text-blue-400" style="font-size:16px"></i>
                                        Profil Saya
                                    </a>
                                    <a href="{{ route('transaction.history') }}" role="menuitem"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                               hover:bg-blue-50 hover:text-[#0f3460] transition-colors">
                                        <i class="ti ti-shopping-bag text-blue-400" style="font-size:16px"></i>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('address.index') }}" role="menuitem"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                               hover:bg-blue-50 hover:text-[#0f3460] transition-colors">
                                        <i class="ti ti-settings text-blue-400" style="font-size:16px"></i>
                                        Alamat
                                    </a>
                                    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
                                        <a href="{{ route('admin.dashboard') }}" role="menuitem"
                                            class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                                   hover:bg-blue-50 hover:text-[#0f3460] transition-colors">
                                            <i class="ti ti-layout-dashboard text-blue-400" style="font-size:16px"></i>
                                            Dashboard Admin
                                        </a>
                                    @endif
                                    <div class="h-px bg-blue-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" role="menuitem"
                                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[13px]
                                                   text-red-600 hover:bg-red-50 transition-colors text-left">
                                            <i class="ti ti-logout" style="font-size:16px;color:#fca5a5"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="px-4 py-2.5 border-b border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest">Mode Tamu
                                    </p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('login') }}"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                               hover:bg-blue-50 transition-colors">
                                        <i class="ti ti-login text-blue-400" style="font-size:16px"></i>
                                        Masuk
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                            class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700
                                                   hover:bg-blue-50 transition-colors">
                                            <i class="ti ti-user-plus text-blue-400" style="font-size:16px"></i>
                                            Daftar
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>

                </div>

                {{-- ===== MOBILE: Cart icon (kanan) ===== --}}
                <div class="flex md:hidden">
                    @auth
                        <button id="cart-button-mobile"
                            class="relative w-9 h-9 rounded-lg flex items-center justify-center
                                   bg-white/10 border border-white/15 text-white/85
                                   hover:bg-white/18 hover:text-white transition-all duration-150"
                            title="Keranjang">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                                <line x1="3" y1="6" x2="21" y2="6" />
                                <path d="M16 10a4 4 0 01-8 0" />
                            </svg>
                            @if (Auth::user()->cart?->items->count() > 0)
                                <span
                                    class="absolute -top-1.5 -right-1.5 min-w-[17px] h-[17px] px-1
                                             bg-blue-500 text-white text-[10px] font-bold rounded-full
                                             flex items-center justify-center border-2 border-[#0f3460]">
                                    {{ Auth::user()->cart->items->count() }}
                                </span>
                            @endif
                        </button>
                    @else
                        {{-- Guest: tampilkan icon user sebagai spacer biar logo tetap center --}}
                        <a href="{{ route('login') }}"
                            class="w-9 h-9 rounded-lg flex items-center justify-center
                                   bg-white/10 border border-white/15 text-white/85
                                   hover:bg-white/18 hover:text-white transition-all duration-150">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </a>
                    @endauth
                </div>

            </div>
        </div>

        @include('cart.modal-cart')
    </nav>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Cart modal — handle dua button (desktop + mobile)
        const cartBtns = [
            document.getElementById('cart-button'),
            document.getElementById('cart-button-mobile'),
        ];
        const modal = document.getElementById('cart-modal');
        const closeBtn = document.getElementById('close-cart-modal');
        if (!modal) return;

        cartBtns.forEach(btn => {
            btn?.addEventListener('click', e => {
                e.preventDefault();
                modal.classList.remove('hidden');
            });
        });

        closeBtn?.addEventListener('click', () => modal.classList.add('hidden'));

        window.addEventListener('click', e => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>
