<!-- Container Utama -->
<div class="relative">
    <!-- Tombol Back di Pojok Kiri Atas -->


    <!-- Looping untuk menampilkan produk dalam bentuk card -->
    <div class="flex flex-wrap justify-center">
        @foreach ($products as $product)
            <div
                class="relative m-4 flex w-full max-w-[150px] sm:max-w-[200px] md:max-w-[250px] lg:max-w-[250px] xl:max-w-[250px] flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md">
                <!-- Container utama dari card produk dengan berbagai ukuran maksimum pada layar yang berbeda -->

                <a class="relative mx-2 mt-2 flex h-32 sm:h-40 md:h-48 lg:h-48 xl:h-48 overflow-hidden rounded-xl"
                    href="{{ url('/product/' . $product->id) }}">
                    <!-- Gambar produk dengan ukuran yang menyesuaikan tinggi pada layar yang berbeda -->
                    <img class="object-cover w-full h-full" src="{{ asset('storage/' . $product->image_url) }}"
                        alt="{{ $product->name }}" />
                </a>

                <div class="mt-3 px-4 pb-4">
                    <!-- Bagian deskripsi produk -->
                    <a href="#">
                        <!-- Judul produk -->
                        <h5 class="text-sm sm:text-base md:text-lg lg:text-lg xl:text-lg tracking-tight text-slate-900">
                            {{ $product->name }}</h5>
                    </a>

                    <div class="mt-2 mb-4 flex items-center justify-between">
                        <!-- Harga produk -->
                        <p>
                            <span
                                class="text-lg sm:text-xl md:text-2xl lg:text-2xl xl:text-2xl font-bold text-slate-900">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    @guest
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center rounded-md bg-slate-900 px-2 sm:px-4 lg:px-4 xl:px-4 py-1 sm:py-2 lg:py-2 xl:py-2 text-center text-xs sm:text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="mr-1 sm:mr-2 h-3 sm:h-4 lg:h-4 xl:h-4 w-3 sm:w-4 lg:w-4 xl:w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to cart
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route('cart.add', ['product_id' => $product->id]) }}"
                            class="flex items-center justify-center rounded-md bg-slate-900 px-2 sm:px-4 lg:px-4 xl:px-4 py-1 sm:py-2 lg:py-2 xl:py-2 text-center text-xs sm:text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="mr-1 sm:mr-2 h-3 sm:h-4 lg:h-4 xl:h-4 w-3 sm:w-4 lg:w-4 xl:w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to cart
                        </a>
                    @endauth



                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
