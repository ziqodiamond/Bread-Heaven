<div class="relative max-w-7xl mx-auto px-4">
    <!-- Section Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Produk Terbaru</h2>
        <p class="text-gray-600">Koleksi produk terbaru kami yang fresh dan berkualitas</p>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-lg">Produk tidak tersedia</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if (method_exists($products, 'links'))
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @endif

    <!-- View All Button (for home page only) -->
    @if (request()->route()->getName() === 'home' && $products->count() > 0)
        <div class="mt-8 flex justify-center">
            <a href="{{ route('products') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                Lihat Semua Produk
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    @endif
</div>
