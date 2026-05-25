{{-- 
    Product Card Component
    Displays a product in a card format with price, discount, stock info, and action buttons.
    
    Props:
    - product: Product object (required)
    - showBuyNow: Show "Buy Now" button (default: true)
    - showCart: Show "Add to Cart" button (default: true)
--}}

@props([
    'product',
    'showBuyNow' => true,
    'showCart' => true,
])

<div class="relative flex w-full max-w-sm flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md hover:shadow-lg transition-shadow">
    
    <!-- Image + Discount Badge -->
    <a href="{{ url('/product/' . $product->id) }}" class="relative group">
        <div class="relative overflow-hidden rounded-t-lg bg-gray-100">
            <img 
                class="aspect-square w-full object-cover group-hover:scale-105 transition-transform duration-200" 
                src="{{ $product->thumbnail ?? $product->image_url }}" 
                alt="{{ $product->name }}" 
            />
            
            <!-- Discount Badge -->
            @if ($product->active_discount_type !== 'none')
                <div class="absolute top-2 right-2 z-10">
                    @if ($product->active_discount_type === 'flash_sale')
                        <span class="inline-block px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                            ⚡ Flash Sale
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 text-xs font-bold text-white bg-orange-500 rounded-full">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </a>

    <!-- Product Info -->
    <div class="flex flex-col gap-3 p-4 flex-grow">
        <!-- Product Name -->
        <a href="{{ url('/product/' . $product->id) }}" class="group/name">
            <h5 class="font-semibold text-gray-900 line-clamp-2 group-hover/name:text-blue-600 transition-colors">
                {{ $product->name }}
            </h5>
        </a>
        
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

        <!-- Action Buttons -->
        <div class="mt-auto flex gap-2">
            @if ($showBuyNow)
                <a href="{{ url('/product/' . $product->id) }}" class="flex-1 flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 transition-colors {{ !$product->in_stock ? 'opacity-50 cursor-not-allowed' : '' }}">
                    💳 Beli Sekarang
                </a>
            @endif

            @if ($showCart)
                @guest
                    <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Keranjang
                    </a>
                @endguest
                @auth
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" {{ !$product->in_stock ? 'disabled' : '' }} class="flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-gray-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Keranjang
                        </button>
                    </form>
                @endauth
            @endif
        </div>
    </div>
</div>
