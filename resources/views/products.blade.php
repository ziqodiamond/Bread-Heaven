<x-layout>
    @if (session('success'))
        <div id="success-alert"
            class="alert alert-success fixed top-4 right-4 z-50 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Semua Produk</h1>
                <p class="text-gray-600">Temukan produk favorit Anda dari koleksi lengkap kami</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <form method="GET" action="{{ route('products') }}" class="bg-white p-6 rounded-lg shadow-sm sticky top-4">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Cari Produk</label>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}"
                                placeholder="Nama produk..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Category Filter -->
                        @if($categories->isNotEmpty())
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Kategori</label>
                                <select 
                                    name="category" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Price Range Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Harga</label>
                            <div class="flex gap-2">
                                <input 
                                    type="number" 
                                    name="min_price" 
                                    value="{{ $min_price }}"
                                    placeholder="Min"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                />
                                <input 
                                    type="number" 
                                    name="max_price" 
                                    value="{{ $max_price }}"
                                    placeholder="Max"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                />
                            </div>
                        </div>

                        <!-- Discount Type Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Tipe Diskon</label>
                            <select 
                                name="discount_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Semua Produk</option>
                                <option value="flash_sale" {{ $discount_type === 'flash_sale' ? 'selected' : '' }}>
                                    ⚡ Flash Sale
                                </option>
                                <option value="discount" {{ $discount_type === 'discount' ? 'selected' : '' }}>
                                    Diskon Khusus
                                </option>
                                <option value="none" {{ $discount_type === 'none' ? 'selected' : '' }}>
                                    Harga Normal
                                </option>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Urutkan</label>
                            <select 
                                name="sort" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>
                                    Terbaru
                                </option>
                                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>
                                    Terlama
                                </option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>
                                    Harga: Rendah ke Tinggi
                                </option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>
                                    Harga: Tinggi ke Rendah
                                </option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-2">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                            >
                                Terapkan Filter
                            </button>
                            <a 
                                href="{{ route('products') }}" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg font-semibold hover:bg-gray-300 transition-colors text-center"
                            >
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <div class="col-span-full bg-white p-12 rounded-lg text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-lg font-semibold text-gray-900">Produk tidak ditemukan</p>
                                <p class="text-gray-600 mt-2">Coba ubah filter atau pencarian Anda</p>
                                <a href="{{ route('products') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Lihat Semua Produk
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($products->hasPages())
                        <div class="mt-12">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>
</x-layout>
