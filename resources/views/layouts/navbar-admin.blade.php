{{-- Sidebar menggunakan Alpine.js untuk toggle mobile --}}
<div x-data="{ open: false }">

    {{-- Overlay saat mobile sidebar terbuka --}}
    <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-20 bg-black/30 lg:hidden"
        style="display: none;"></div>

    {{-- Tombol hamburger — hanya tampil di mobile --}}
    <button @click="open = true"
        class="fixed top-4 left-4 z-30 flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 shadow-sm lg:hidden">
        {{-- Icon hamburger manual agar tidak perlu dependency tambahan --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{--
        Sidebar:
        - Desktop: selalu tampil (translate-x-0)
        - Mobile: tersembunyi di kiri (-translate-x-full), muncul saat open = true
    --}}
    <aside :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 flex h-screen w-60 flex-col justify-between
           overflow-y-auto
           border-r border-gray-100 bg-white transition-transform duration-200 ease-in-out
           lg:translate-x-0 lg:transition-none">
        {{-- Bagian atas: logo + navigasi --}}
        <div>

            {{-- Logo area --}}
            <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900">
                    {{-- Ganti dengan logo jika ada --}}
                    <img src="{{ asset('storage/general_images/logo.png') }}" alt="Bread Heaven"
                        class="h-5 w-5 object-contain">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 leading-tight">Bread Heaven</p>
                    <p class="text-xs text-gray-400">Admin Panel</p>
                </div>
                {{-- Tombol tutup sidebar — hanya mobile --}}
                <button @click="open = false" class="ml-auto text-gray-400 hover:text-gray-600 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Navigasi utama --}}
            <nav class="px-3 pt-5 space-y-0.5">

                <a href="{{ route('admin.dashboard') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.dashboard') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 9.5L12 4l9 5.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" />
                    </svg>
                    Dashboard
                </a>

                {{-- Label grup --}}
                <p class="px-3 pt-4 pb-1 text-[10.5px] font-medium uppercase tracking-widest text-gray-400">
                    Management
                </p>

                <a href="{{ route('admin.management.users.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.management.users.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20H7a4 4 0 01-4-4v0a4 4 0 014-4h10a4 4 0 014 4v0a4 4 0 01-4 4zM12 12a4 4 0 100-8 4 4 0 000 8z" />
                    </svg>
                    Manage Users
                </a>

                <a href="{{ route('admin.management.payment-methods.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.management.payment-methods.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 10h18M7 15h1m4 0h1M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                    </svg>
                    Payment Methods
                </a>

                <a href="{{ route('admin.management.shipping-methods.index') }} " @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.management.shipping-methods.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M5 17H3a2 2 0 01-2-2V7a2 2 0 012-2h11v12H5zm0 0a2 2 0 104 0m6 0a2 2 0 104 0M13 5l3 4h4l-1 4h-6V5z" />
                    </svg>
                    Shipping Methods
                </a>

                <a href="{{ route('admin.management.products.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.management.products.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Products
                </a>

                <a href="{{ route('admin.management.stores.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.management.stores.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Stores
                </a>

                <p class="px-3 pt-4 pb-1 text-[10.5px] font-medium uppercase tracking-widest text-gray-400">
                    Transactions
                </p>

                {{-- Orders - icon shopping bag / receipt --}}
                <a href="{{ route('admin.orders.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
           hover:bg-gray-50 hover:text-gray-800
           {{ request()->routeIs('admin.orders.index') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Orders
                </a>

                {{-- Payments - icon credit card --}}
                <a href="{{ route('admin.payment.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
           hover:bg-gray-50 hover:text-gray-800
           {{ request()->routeIs('admin.payment.index') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Payments
                </a>

                {{-- Shipping - icon truck --}}
                <a href="{{ route('admin.shipment.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
           hover:bg-gray-50 hover:text-gray-800
           {{ request()->routeIs('admin.shipment.index') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 .001M13 16l2 .001M13 16V8h3l3 5v3h-2" />
                    </svg>
                    Shipping
                </a>



                <p class="px-3 pt-4 pb-1 text-[10.5px] font-medium uppercase tracking-widest text-gray-400">
                    Finance
                </p>

                <a href="{{ route('admin.transactions.index') }}" @click="open = false"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-500
                           hover:bg-gray-50 hover:text-gray-800
                           {{ request()->routeIs('admin.transaction.*') ? 'bg-gray-50 text-gray-900 font-medium' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 14l-4-4 4-4m6 8l4-4-4-4M14 6l-4 12" />
                    </svg>
                    Transactions
                </a>

            </nav>
        </div>

        {{-- Footer: back to home --}}
        <div class="border-t border-gray-100 p-3">
            <a href="{{ route('home') }}" @click="open = false"
                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Home
            </a>
        </div>

    </aside>
</div>
