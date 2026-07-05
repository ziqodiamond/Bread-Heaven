<x-layout>
    @if (session('success'))
        <div id="success-alert"
            class="alert alert-success fixed top-4 right-4 z-50 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @php
        // Determine badge colors based on flashsale badge_color
        $badgeColor = $flashSale->badge_color ?? '#FF6B6B';
        
        // Extract color components for styling
        $startColor = $badgeColor;
        $endColor = $badgeColor;
        
        // Determine flashsale state
        $isRunning = $flashSale->is_running;
        $hasEnded = $flashSale->has_ended;
        $hasStarted = $flashSale->has_started;
    @endphp

    <!-- Banner Section -->
    @if ($flashSale->banner)
        <div class="w-full bg-gray-200">
            <img src="{{ Storage::url($flashSale->banner) }}" 
                alt="{{ $flashSale->name }}"
                class="w-full h-auto object-cover"
            />
        </div>
    @endif

    <div class="min-h-screen py-8" style="background: linear-gradient(135deg, {{ $badgeColor }}15 0%, {{ $badgeColor }}05 100%);">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Flash Sale Header -->
            <div class="mb-8 bg-white rounded-lg shadow-sm p-8" style="border-top: 4px solid {{ $badgeColor }};">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold text-white"
                                style="background-color: {{ $badgeColor }};">
                                ⚡ Flash Sale
                            </span>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $flashSale->name }}</h1>
                        @if ($flashSale->label)
                            <p class="text-lg text-gray-600 mb-4">{{ $flashSale->label }}</p>
                        @endif
                        @if ($flashSale->description)
                            <p class="text-gray-600">{{ $flashSale->description }}</p>
                        @endif
                    </div>

                    <!-- Status and Countdown -->
                    <div class="md:text-right" x-data="{
                        startTime: {{ $flashSale->start_at->timestamp }},
                        endTime: {{ $flashSale->end_at->timestamp }},
                        isRunning: {{ $isRunning ? 'true' : 'false' }},
                        hasEnded: {{ $hasEnded ? 'true' : 'false' }},
                        hasStarted: {{ $hasStarted ? 'true' : 'false' }},
                        countdown: '',
                        
                        formatCountdown(seconds) {
                            const days = Math.floor(seconds / 86400);
                            const hours = Math.floor((seconds % 86400) / 3600);
                            const minutes = Math.floor((seconds % 3600) / 60);
                            const secs = seconds % 60;
                            
                            if (days > 0) return `${days}d ${hours}h`;
                            if (hours > 0) return `${hours}h ${minutes}m`;
                            return `${minutes}m ${secs}s`;
                        },
                        
                        updateCountdown() {
                            const now = Math.floor(Date.now() / 1000);
                            let remaining = 0;
                            
                            if (this.isRunning) {
                                remaining = this.endTime - now;
                                this.countdown = this.formatCountdown(Math.max(0, remaining));
                                if (remaining <= 0) {
                                    this.hasEnded = true;
                                    this.isRunning = false;
                                }
                            } else if (!this.hasStarted) {
                                remaining = this.startTime - now;
                                this.countdown = this.formatCountdown(Math.max(0, remaining));
                                if (remaining <= 0) {
                                    this.hasStarted = true;
                                    this.isRunning = true;
                                }
                            }
                        },
                        
                        init() {
                            this.updateCountdown();
                            setInterval(() => this.updateCountdown(), 1000);
                        }
                    }"
                        x-init="init()"
                        class="space-y-4">
                        
                        <!-- Status Badge -->
                        <div>
                            <template x-if="hasEnded && !isRunning">
                                <div class="inline-block px-4 py-2 rounded-full text-sm font-semibold text-white"
                                    style="background-color: #999;">
                                    Flash Sale Telah Berakhir
                                </div>
                            </template>
                            <template x-if="isRunning">
                                <div class="inline-block px-4 py-2 rounded-full text-sm font-semibold text-white"
                                    style="background-color: {{ $badgeColor }};">
                                    Sedang Berlangsung
                                </div>
                            </template>
                            <template x-if="!isRunning && !hasStarted">
                                <div class="inline-block px-4 py-2 rounded-full text-sm font-semibold text-white"
                                    style="background-color: #666;">
                                    Akan Datang
                                </div>
                            </template>
                        </div>

                        <!-- Countdown -->
                        <div class="text-center md:text-right">
                            <p class="text-gray-600 text-sm mb-1">
                                <template x-if="isRunning">
                                    <span>Berakhir dalam:</span>
                                </template>
                                <template x-if="!hasStarted">
                                    <span>Dimulai dalam:</span>
                                </template>
                            </p>
                            <p class="text-3xl font-bold" style="color: {{ $badgeColor }};"
                                x-text="countdown"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <form method="GET" action="{{ route('flashsale.show', $flashSale) }}" 
                        class="bg-white p-6 rounded-lg shadow-sm sticky top-4">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Cari Produk</label>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}"
                                placeholder="Nama produk..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-colors"
                                style="focus:ring-color: {{ $badgeColor }};"
                            />
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Urutkan</label>
                            <select 
                                name="sort" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 transition-colors"
                                style="focus:ring-color: {{ $badgeColor }};"
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
                                <option value="discount" {{ $sort === 'discount' ? 'selected' : '' }}>
                                    Diskon Terbesar
                                </option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-2">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 text-white rounded-lg font-semibold hover:opacity-90 transition-opacity"
                                style="background-color: {{ $badgeColor }};"
                            >
                                Terapkan Filter
                            </button>
                            <a 
                                href="{{ route('flashsale.show', $flashSale) }}" 
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
                        @forelse($items as $item)
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                                <!-- Product Image Container -->
                                <div class="relative bg-gray-100 overflow-hidden h-48">
                                    @if ($item->product && $item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" 
                                            alt="{{ $item->product_name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Discount Badge -->
                                    @if ($item->discount_percentage > 0)
                                        <div class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-bold text-white"
                                            style="background-color: {{ $badgeColor }};">
                                            -{{ $item->discount_percentage }}%
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    @if (!$isRunning)
                                        <div class="absolute top-2 left-2 px-2 py-1 rounded text-xs font-semibold text-white bg-gray-600">
                                            @if ($hasEnded)
                                                Berakhir
                                            @else
                                                Rahasia
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2">
                                        {{ $item->product_name }}
                                    </h3>

                                    <!-- Pricing -->
                                    <div class="mb-3">
                                        @if ($isRunning)
                                            <!-- Show actual prices during running -->
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-lg font-bold text-gray-900">
                                                    Rp {{ number_format($item->sale_price, 0, ',', '.') }}
                                                </span>
                                                @if ($item->original_price > $item->sale_price)
                                                    <span class="text-sm text-gray-500 line-through">
                                                        Rp {{ number_format($item->original_price, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <!-- Show "Harga Rahasia" when not running -->
                                            <div class="text-gray-500 text-sm font-medium">
                                                Rp ???
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Quantity Available -->
                                    <div class="text-xs text-gray-600 mb-3">
                                        Stok: <span class="font-semibold">{{ $item->stock }}</span>
                                    </div>

                                    <!-- Add to Cart Button -->
                                    @if ($item->product)
                                        <a href="{{ route('product.show', $item->product->id) }}"
                                            class="w-full block px-3 py-2 text-center text-white rounded-lg font-medium hover:opacity-90 transition-opacity text-sm"
                                            style="background-color: {{ $badgeColor }};"
                                        >
                                            Lihat Detail
                                        </a>
                                    @else
                                        <button disabled
                                            class="w-full px-3 py-2 bg-gray-300 text-gray-600 rounded-lg font-medium text-sm cursor-not-allowed"
                                        >
                                            Tidak Tersedia
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white p-12 rounded-lg text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-lg font-semibold text-gray-900">Produk tidak ditemukan</p>
                                <p class="text-gray-600 mt-2">Coba ubah pencarian atau filter Anda</p>
                                <a href="{{ route('flashsale.show', $flashSale) }}" class="mt-4 inline-block px-6 py-2 text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: {{ $badgeColor }};"
                                >
                                    Kembali ke Flash Sale
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($items->hasPages())
                        <div class="mt-12">
                            {{ $items->appends(request()->query())->links() }}
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
