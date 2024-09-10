<section class="py-8 bg-white md:py-16 dark:bg-gray-900 antialiased">

    <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">
        <a href="/products"
            class="absolute top-20 left-2 inline-flex items-center justify-center p-2 text-gray-800 bg-white rounded-full shadow-lg hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>
        </a>

        <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">

            <!-- Gambar Produk -->
            <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                <img class="w-full dark:hidden object-cover rounded-xl"
                    src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" />
                <img class="w-full hidden dark:block object-cover rounded-xl"
                    src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" />
            </div>

            <!-- Detail Produk -->
            <div class="mt-6 sm:mt-8 lg:mt-0">
                <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">
                    {{ $product->name }}
                </h1>
                <div class="mt-4 sm:flex sm:items-center sm:gap-4">
                    <p class="text-3xl font-extrabold text-gray-900 dark:text-white">
                        ${{ number_format($product->price, 2) }}
                    </p>
                    <!-- Tambahkan komponen rating jika tersedia -->
                </div>

                <div class="mt-6 sm:flex sm:items-center sm:gap-4">
                    <a href="#"
                        class="flex items-center justify-center py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700"
                        role="button">
                        Add to favorites
                    </a>

                    <a href="{{ route('cart.add', ['product_id' => $product->id]) }}" class="mt-4 sm:mt-0">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit"
                            class="flex items-center justify-center py-2.5 px-5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Add to cart
                        </button>
                    </a>
                </div>

                <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                <p class="mb-6 text-gray-500 dark:text-gray-400">
                    {{ $product->description }}
                </p>

                <!-- Tambahkan informasi tambahan jika diperlukan -->
            </div>
        </div>
    </div>
</section>
