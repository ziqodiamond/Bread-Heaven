<!-- Side menu content -->
<nav class="bg-orange-300" x-data="{ isOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo and navigation links -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-20 w-25" src="{{ asset('storage/general_images/logo.png') }}" alt="Bread Heaven">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <x-navlink href="/" :active="request()->is('/')">Beranda</x-navlink>
                        <x-navlink href="/products" :active="request()->is('products')">Product</x-navlink>
                        @auth
                            <x-navlink href="/about" :active="request()->is('about')">Riwayat Transaksi</x-navlink>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Cart and Profile dropdown -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- Cart button -->
                    <div class="relative">
                        <x-navlink href="#" id="cart-button" :active="request()->is('cart')">
                            <span>
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                                </svg>

                            </span>
                            @if (Auth::check() && Auth::user()->cart && Auth::user()->cart->items->count() > 0)
                                <span
                                    class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                    {{ Auth::user()->cart->items->count() }}
                                </span>
                            @endif
                        </x-navlink>
                    </div>
                @endauth


                @include('cart.modal-cart')

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const cartButton = document.getElementById('cart-button');
                        const cartModal = document.getElementById('cart-modal');
                        const closeModalButton = document.getElementById('close-cart-modal');

                        function openCartModal() {
                            cartModal.classList.remove('hidden');
                        }

                        function closeCartModal() {
                            cartModal.classList.add('hidden');
                        }

                        cartButton.addEventListener('click', function(event) {
                            event.preventDefault();
                            openCartModal();
                        });

                        closeModalButton.addEventListener('click', function() {
                            closeCartModal();
                        });

                        window.addEventListener('click', function(event) {
                            if (event.target === cartModal) {
                                closeCartModal();
                            }
                        });
                    });
                </script>

                <!-- Profile dropdown -->
                <div class="relative" x-data="{ isOpen: false }" @click.away="isOpen = false">
                    <button type="button" @click="isOpen = !isOpen"
                        class="relative flex max-w-xs items-center rounded-full bg-orange-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                        <span class="sr-only">Open user menu</span>
                        @auth
                            <img class="h-8 w-8 rounded-full"
                                src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="User profile photo">
                        @else
                            <img class="h-8 w-8 rounded-full" src="{{ asset('storage/profile_photos/guest.jpg') }}"
                                alt="Default profile photo">
                        @endauth
                    </button>
                    <div x-show="isOpen" x-transition:enter="transition ease-out duration-100 transform"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75 transform"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                        @if (Route::has('login'))
                            <div class="space-y-1">
                                @auth
                                    <div
                                        class="block px-4 py-2 text-sm text-gray-700 font-semibold border-b border-gray-300">
                                        Hi!, {{ Auth::user()->name }}
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700"
                                        role="menuitem">Your Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700"
                                        role="menuitem">Settings</a>
                                    @if (Auth::user()->role == 'admin')
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-sm text-gray-700" role="menuitem">Dashboard</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            role="menuitem">Sign Out</a>
                                    </form>
                                @else
                                    <div
                                        class="block px-4 py-2 text-sm text-gray-700 font-semibold border-b border-gray-300">
                                        Guest Mode
                                    </div>
                                    <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700"
                                        role="menuitem">Sign In</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700"
                                            role="menuitem">Register</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex md:hidden">
                <button type="button" @click="isOpen = !isOpen"
                    class="relative inline-flex items-center justify-center rounded-md bg-orange-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg :class="{ 'hidden': isOpen, 'block': !isOpen }" class="block h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg :class="{ 'block': isOpen, 'hidden': !isOpen }" class="hidden h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="isOpen" class="md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <x-nav-link-mobile href="/" :active="request()->is('/')">Beranda</x-nav-link-mobile>
            <x-nav-link-mobile href="/products" :active="request()->is('products')">Products</x-nav-link-mobile>
            @auth
                <x-nav-link-mobile href="/about" :active="request()->is('about')">Riwayat Transaksi</x-nav-link-mobile>
            @endauth
        </div>

        <div class="flex items-center px-5">
            <div class="flex-shrink-0">
                @auth
                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                        alt="User profile photo">
                @else
                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/profile_photos/guest.jpg') }}"
                        alt="Default profile photo">
                @endauth
            </div>
            @if (Route::has('login'))
                @auth
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white">{{ Auth::user()->username }}</div>
                        <div class="text-sm font-medium leading-none text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="ml-auto rounded-md bg-orange-800 p-1 text-gray-400 hover:bg-orange-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">Logout</button>
                    </form>
                @else
                    <div class="ml-3">
                        <a href="{{ route('login') }}" class="text-base font-medium text-white hover:text-gray-200">Sign
                            In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="ml-4 text-base font-medium text-white hover:text-gray-200">Register</a>
                        @endif
                    </div>
                @endauth
            @endif
        </div>
    </div>
</nav>
