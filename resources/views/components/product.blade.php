<!-- Container Utama -->
<div class="relative">
    <!-- Looping untuk menampilkan produk dalam bentuk card -->
    <div class="flex flex-wrap justify-center gap-4">
        @forelse ($products as $product)
            <div class="relative flex w-full max-w-sm flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md hover:shadow-lg transition-shadow">
                
                <!-- Link Produk + Badge Diskon -->
                <a href="{{ url('/product/' . $product->id) }}" class="relative group">
                    <div class="relative overflow-hidden rounded-t-lg">
                        <img 
                            class="aspect-square w-full object-cover group-hover:scale-105 transition-transform duration-200" 
                            src="{{ $product->thumbnail }}" 
                            alt="{{ $product->name }}" 
                        />
                        
                        <!-- Discount Badge -->
                        @if ($product->active_discount_type !== 'none')
                            <div class="absolute top-2 right-2">
                                @if ($product->active_discount_type === 'flash_sale')
                                    <span class="inline-block px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                                        ⚡ Flash Sale
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-bold text-white bg-orange-500 rounded-full">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>

                <!-- Product Info -->
                <div class="flex flex-col gap-3 p-4 flex-grow">
                    <h5 class="font-semibold text-gray-900 line-clamp-2 hover:text-blue-600">
                        {{ $product->name }}
                    </h5>
                    
                    <!-- Price Section -->
                    <div class="flex flex-col gap-1">
                        @if ($product->active_discount_type !== 'none')
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-red-600">
                                    Rp {{ number_format($product->resolved_price, 0, ',', '.') }}
                                </span>
                                <span class="text-sm text-gray-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>
                        @else
                            <span class="text-lg font-bold text-gray-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Stock Info -->
                    <div class="text-xs {{ $product->in_stock ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->in_stock ? 'Stok: ' . $product->stock . ' tersedia' : 'Stok Habis' }}
                    </div>

                    <!-- Add to Cart Button -->
                    <div class="mt-auto">
                        @guest
                            <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Keranjang
                            </a>
                        @endguest
                        @auth
                            <form action="{{ route('cart.add') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" {{ !$product->in_stock ? 'disabled' : '' }} class="flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Keranjang
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="w-full text-center py-8 text-gray-500">
                Produk tidak tersedia
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if (method_exists($products, 'links'))
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif
</div>
