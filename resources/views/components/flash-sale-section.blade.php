{{-- 
    Flash Sale Section Component
    Displays a single flash sale as a full section with product cards
    
    Props:
    - flashSale: FlashSale object (required)
    - maxProducts: Max products to show (default: 12)
--}}

@props(['flashSale', 'maxProducts' => 12])

@if ($flashSale->is_running)
    <section class="py-12 border-b border-gray-200 last:border-b-0">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Flash Sale Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <!-- Badge & Title -->
                    <div class="flex items-center gap-3">
                        <div class="inline-flex items-center gap-2 rounded-lg px-4 py-2"
                            style="background: linear-gradient(to right, {{ getColorRGB($flashSale->badge_color, 'start') }}, {{ getColorRGB($flashSale->badge_color, 'end') }});">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="13 2 13 9 20 9"></polyline>
                            </svg>
                            <span class="font-bold text-white">{{ $flashSale->name }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $flashSale->label }}</span>
                    </div>

                    <!-- View All Button -->
                    @if (!$flashSale->has_ended)
                        <a href="{{ route('flash-sale.show', $flashSale->slug) }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-2">
                            Lihat Selengkapnya →
                        </a>
                    @endif
                </div>

                <!-- Description (if exists) -->
                @if ($flashSale->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $flashSale->description }}</p>
                @endif
            </div>

            <!-- Countdown / Status Section -->
            <div class="mb-8 p-4 bg-gradient-to-r rounded-lg"
                style="background: linear-gradient(to right, {{ getColorRGB($flashSale->badge_color, 'start') }}, {{ getColorRGB($flashSale->badge_color, 'end') }});">
                <div class="text-white">
                    @if ($flashSale->is_running)
                        <!-- Countdown for Running Flash Sale -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 bg-yellow-300 rounded-full animate-pulse"></div>
                                <span class="font-semibold">Sedang Berlangsung! 🔥</span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs opacity-90 mb-1">Berakhir dalam:</p>
                                <div class="flex gap-2">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-day"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Hari</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-hour"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Jam</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-min"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Menit</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-sec"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Detik</span>
                                    </div>
                                </div>
                                <input type="hidden" class="flash-end-time" data-flashsale-id="{{ $flashSale->id }}"
                                    value="{{ $flashSale->end_at->timestamp }}">
                            </div>
                        </div>
                    @elseif($flashSale->has_started && $flashSale->has_ended)
                        <!-- Ended Flash Sale -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 bg-gray-300 rounded-full"></div>
                                <span class="font-semibold">Flash Sale Selesai ✓</span>
                            </div>
                            <p class="text-sm opacity-90">Berakhir: {{ $flashSale->end_at->format('d M Y H:i') }}</p>
                        </div>
                    @else
                        <!-- Coming Soon Flash Sale -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 bg-blue-300 rounded-full animate-pulse"></div>
                                <span class="font-semibold">Akan Segera Dimulai! ⏰</span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs opacity-90 mb-1">Dimulai dalam:</p>
                                <div class="flex gap-2">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-day-start"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Hari</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-hour-start"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Jam</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-min-start"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Menit</span>
                                    </div>
                                    <span class="font-bold">:</span>
                                    <div class="flex flex-col items-center">
                                        <div class="bg-white bg-opacity-20 rounded px-2 py-1 min-w-[50px]">
                                            <span class="font-bold countdown-sec-start"
                                                data-flashsale-id="{{ $flashSale->id }}">00</span>
                                        </div>
                                        <span class="text-xs mt-1">Detik</span>
                                    </div>
                                </div>
                                <input type="hidden" class="flash-start-time" data-flashsale-id="{{ $flashSale->id }}"
                                    value="{{ $flashSale->start_at->timestamp }}">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($flashSale->items()->limit($maxProducts)->get() as $item)
                    @php
                        // Get the actual product to pass to card component
                        $product = $item->product ?? null;
                    @endphp
                    @if ($product)
                        <x-product-card :product="$product" />
                    @endif
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        @if ($flashSale->has_ended)
                            <p class="text-lg font-semibold">Flash Sale Selesai</p>
                        @elseif(!$flashSale->has_started)
                            <p class="text-lg font-semibold">Produk akan segera tersedia</p>
                        @else
                            <p class="text-lg font-semibold">Tidak ada produk</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- View All Button (at bottom) -->
            @if (!$flashSale->has_ended && $flashSale->items()->count() > $maxProducts)
                <div class="mt-8 flex justify-center">
                    <a href="{{ route('flash-sale.show', $flashSale->slug) }}"
                        class="inline-flex items-center gap-2 rounded-lg px-8 py-3 text-lg font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background: linear-gradient(to right, {{ getColorRGB($flashSale->badge_color, 'start') }}, {{ getColorRGB($flashSale->badge_color, 'end') }});">
                        Lihat Semua Produk
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>
@endif

<script>
    // Countdown timer for running flash sales
    function updateFlashSaleCountdowns() {
        const now = Math.floor(Date.now() / 1000);

        // Update end time countdowns
        document.querySelectorAll('.flash-end-time').forEach(element => {
            const flashsaleId = element.dataset.flashsaleId;
            const endTime = parseInt(element.value);
            const remaining = endTime - now;

            if (remaining > 0) {
                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;

                const dayEl = document.querySelector(`.countdown-day[data-flashsale-id="${flashsaleId}"]`);
                const hourEl = document.querySelector(`.countdown-hour[data-flashsale-id="${flashsaleId}"]`);
                const minEl = document.querySelector(`.countdown-min[data-flashsale-id="${flashsaleId}"]`);
                const secEl = document.querySelector(`.countdown-sec[data-flashsale-id="${flashsaleId}"]`);

                if (dayEl) dayEl.textContent = String(days).padStart(2, '0');
                if (hourEl) hourEl.textContent = String(hours).padStart(2, '0');
                if (minEl) minEl.textContent = String(minutes).padStart(2, '0');
                if (secEl) secEl.textContent = String(seconds).padStart(2, '0');
            }
        });

        // Update start time countdowns (for coming soon)
        document.querySelectorAll('.flash-start-time').forEach(element => {
            const flashsaleId = element.dataset.flashsaleId;
            const startTime = parseInt(element.value);
            const remaining = startTime - now;

            if (remaining > 0) {
                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;

                const dayEl = document.querySelector(
                    `.countdown-day-start[data-flashsale-id="${flashsaleId}"]`);
                const hourEl = document.querySelector(
                    `.countdown-hour-start[data-flashsale-id="${flashsaleId}"]`);
                const minEl = document.querySelector(
                    `.countdown-min-start[data-flashsale-id="${flashsaleId}"]`);
                const secEl = document.querySelector(
                    `.countdown-sec-start[data-flashsale-id="${flashsaleId}"]`);

                if (dayEl) dayEl.textContent = String(days).padStart(2, '0');
                if (hourEl) hourEl.textContent = String(hours).padStart(2, '0');
                if (minEl) minEl.textContent = String(minutes).padStart(2, '0');
                if (secEl) secEl.textContent = String(seconds).padStart(2, '0');
            }
        });
    }

    // Update immediately and then every second
    updateFlashSaleCountdowns();
    setInterval(updateFlashSaleCountdowns, 1000);
</script>
