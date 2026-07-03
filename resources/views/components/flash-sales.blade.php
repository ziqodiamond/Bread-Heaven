@php
    $visibleFlashSales = $flashSales->filter(function($fs) {
        return $fs->show_in_homepage && !$fs->has_ended;
    });
@endphp

@if($visibleFlashSales->isNotEmpty())
<div class="relative mb-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2 rounded-lg px-4 py-2" style="background: linear-gradient(to right, var(--color-start, rgb(239, 68, 68)), var(--color-end, rgb(249, 115, 22)));">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                    <polyline points="13 2 13 9 20 9"></polyline>
                </svg>
                <span class="font-bold text-white">⚡ FLASH SALE</span>
            </div>
            <span class="text-sm font-medium text-gray-600">Penawaran terbatas waktu</span>
        </div>
        <a href="{{ route('products') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
            Lihat semua →
        </a>
    </div>

    <!-- Flash Sales Carousel -->
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max">
            @forelse($visibleFlashSales as $flashSale)
                <div class="flex-shrink-0 w-80">
                    <!-- Flash Sale Card -->
                    <div class="rounded-xl border border-gray-100 bg-white overflow-hidden shadow-md hover:shadow-lg transition-shadow {{ $flashSale->has_ended ? 'opacity-75' : '' }}">
                        <!-- Flash Sale Header -->
                        <div class="relative p-4 text-white" style="background: linear-gradient(to right, var(--color-start), var(--color-end)); --color-start: {{ getColorRGB($flashSale->badge_color, 'start') }}; --color-end: {{ getColorRGB($flashSale->badge_color, 'end') }};">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-bold">{{ $flashSale->name }}</h3>
                                    <p class="text-xs {{ $flashSale->has_ended ? 'text-gray-100' : 'text-white text-opacity-90' }} mt-1">{{ $flashSale->label }}</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 opacity-75" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                                </svg>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mt-3 flex items-center gap-2">
                                @if($flashSale->is_running)
                                    <span class="inline-block px-3 py-1.5 bg-white rounded-full text-xs font-bold animate-pulse" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">
                                        🔥 Sedang Berlangsung
                                    </span>
                                @elseif($flashSale->has_ended)
                                    <span class="inline-block px-3 py-1.5 bg-white text-gray-700 rounded-full text-xs font-bold">
                                        ✓ SELESAI
                                    </span>
                                @elseif(!$flashSale->has_started)
                                    <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold text-white bg-white bg-opacity-30">
                                        ⏰ Akan Datang
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Countdown Section -->
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            @if($flashSale->show_countdown && !$flashSale->has_ended)
                                <div class="text-center">
                                    @if($flashSale->is_running)
                                        <p class="text-xs font-medium text-gray-600 mb-2">Berakhir dalam:</p>
                                        <div class="flex justify-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <div class="bg-white rounded-lg border border-gray-200 px-2 py-1 min-w-[40px]">
                                                    <span class="text-lg font-bold countdown-day" data-flashsale-id="{{ $flashSale->id }}" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">0</span>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1">Hari</span>
                                            </div>
                                            <span class="text-lg font-bold text-gray-400">:</span>
                                            <div class="flex flex-col items-center">
                                                <div class="bg-white rounded-lg border border-gray-200 px-2 py-1 min-w-[40px]">
                                                    <span class="text-lg font-bold countdown-hour" data-flashsale-id="{{ $flashSale->id }}" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">0</span>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1">Jam</span>
                                            </div>
                                            <span class="text-lg font-bold text-gray-400">:</span>
                                            <div class="flex flex-col items-center">
                                                <div class="bg-white rounded-lg border border-gray-200 px-2 py-1 min-w-[40px]">
                                                    <span class="text-lg font-bold countdown-min" data-flashsale-id="{{ $flashSale->id }}" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">0</span>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1">Menit</span>
                                            </div>
                                            <span class="text-lg font-bold text-gray-400">:</span>
                                            <div class="flex flex-col items-center">
                                                <div class="bg-white rounded-lg border border-gray-200 px-2 py-1 min-w-[40px]">
                                                    <span class="text-lg font-bold countdown-sec" data-flashsale-id="{{ $flashSale->id }}" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">0</span>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1">Detik</span>
                                            </div>
                                        </div>
                                        <input type="hidden" class="flash-end-time" data-flashsale-id="{{ $flashSale->id }}" value="{{ $flashSale->end_at->timestamp }}">
                                    @elseif(!$flashSale->has_started)
                                        <div class="text-center py-4">
                                            <div class="mb-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700">
                                                Akan Dimulai
                                            </p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Mulai: {{ $flashSale->start_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Products List -->
                        <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
                            @forelse($flashSale->items()->limit(5)->get() as $item)
                                <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                                    <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}" 
                                        class="h-12 w-12 rounded-lg object-cover flex-shrink-0">
                                    <div class="flex-grow min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 line-clamp-2">
                                            {{ $item->product_name }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-sm font-bold" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">
                                                Rp {{ number_format($item->sale_price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-gray-400 line-through">
                                                Rp {{ number_format($item->original_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            <div class="flex-grow h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full" style="background: {{ getColorRGB($flashSale->badge_color, 'start') }}; width: {{ (($item->stock_limit - $item->remaining_stock) / $item->stock_limit) * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium" style="color: {{ getColorRGB($flashSale->badge_color, 'text') }};">
                                                -{{ $item->discount_percentage }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 text-sm py-4">Tidak ada produk</p>
                            @endforelse

                            @if($flashSale->items()->count() > 5)
                                <p class="text-xs text-center text-gray-500 pt-2">
                                    +{{ $flashSale->items()->count() - 5 }} produk lainnya
                                </p>
                            @endif
                        </div>

                        <!-- View Button -->
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            @if($flashSale->has_ended)
                                <button disabled class="block w-full text-center rounded-lg bg-gray-400 px-4 py-2 text-sm font-bold text-gray-600 cursor-not-allowed">
                                    Flash Sale Selesai
                                </button>
                            @else
                                <a href="{{ route('products') }}#flash-sale-{{ $flashSale->slug }}" 
                                    class="block w-full text-center rounded-lg px-4 py-2 text-sm font-bold text-white hover:opacity-90 transition-opacity"
                                    style="background: linear-gradient(to right, {{ getColorRGB($flashSale->badge_color, 'start') }}, {{ getColorRGB($flashSale->badge_color, 'end') }});">
                                    Lihat Produk
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</div>

<script>
    // Real-time countdown function
    function updateCountdowns() {
        const now = Math.floor(Date.now() / 1000);
        
        document.querySelectorAll('.flash-end-time').forEach(element => {
            const flashsaleId = element.dataset.flashsaleId;
            const endTime = parseInt(element.value);
            const remaining = endTime - now;
            
            if (remaining > 0) {
                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;
                
                document.querySelector(`.countdown-day[data-flashsale-id="${flashsaleId}"]`).textContent = String(days).padStart(2, '0');
                document.querySelector(`.countdown-hour[data-flashsale-id="${flashsaleId}"]`).textContent = String(hours).padStart(2, '0');
                document.querySelector(`.countdown-min[data-flashsale-id="${flashsaleId}"]`).textContent = String(minutes).padStart(2, '0');
                document.querySelector(`.countdown-sec[data-flashsale-id="${flashsaleId}"]`).textContent = String(seconds).padStart(2, '0');
            }
        });
    }

    // Update every second
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
</script>
@endif
