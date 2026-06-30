{{-- 
    Flash Sale Carousel Component (Shopee-style)
    Setiap flash sale (tipe) akan render sebagai section terpisah,
    jadi kalau ada beberapa flash sale aktif, otomatis tersusun ke bawah.

    Props:
    - flashSales: Collection of FlashSale objects (required)
--}}

@props(['flashSales'])

@php
    // Ambil hanya flash sale yang sedang berjalan
    $runningFlashSales = $flashSales->filter(fn($fs) => $fs->is_running ?? false);
    // Standarisasi warna tema flash sale
    $themeColors = [
        'primary' => 'rgb(239, 68, 68)',      // red-600
        'secondary' => 'rgb(234, 88, 12)',    // orange-600
        'accent' => 'rgb(251, 146, 60)',      // orange-400
        'bg_light' => 'rgb(254, 243, 224)',   // orange-50
    ];
@endphp

@if ($runningFlashSales->isNotEmpty())
    {{-- Looping per flash sale, masing-masing jadi 1 section --}}
    @foreach ($runningFlashSales as $flashSale)
        <div class="mb-8" x-data="{
            endTime: {{ $flashSale->end_at->timestamp }},
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            tick() {
                const remaining = this.endTime - Math.floor(Date.now() / 1000);
                if (remaining <= 0) { this.days = this.hours = this.minutes = this.seconds = '00'; return; }
                this.days = String(Math.floor(remaining / 86400)).padStart(2, '0');
                this.hours = String(Math.floor((remaining % 86400) / 3600)).padStart(2, '0');
                this.minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                this.seconds = String(remaining % 60).padStart(2, '0');
            }
        }" x-init="tick();
        setInterval(() => tick(), 1000)">

            {{-- Header: Logo Flash Sale + Countdown + Lihat Semua --}}
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="rgb(239,68,68)">
                            <path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" />
                        </svg>
                        <span class="text-xl font-extrabold italic">
                            <span class="text-red-600">FLASH</span>
                            <span class="text-orange-500">SALE</span>
                        </span>
                    </div>

                    {{-- Kotak Countdown --}}
                    <div class="flex items-center gap-1">
                        <template x-if="days !== '00'">
                            <span class="rounded bg-gray-900 px-2 py-1 text-xs font-bold text-white"
                                x-text="days"></span>
                        </template>
                        <span class="rounded bg-gray-900 px-2 py-1 text-xs font-bold text-white" x-text="hours"></span>
                        <span class="text-xs font-bold text-gray-900">:</span>
                        <span class="rounded bg-gray-900 px-2 py-1 text-xs font-bold text-white"
                            x-text="minutes"></span>
                        <span class="text-xs font-bold text-gray-900">:</span>
                        <span class="rounded bg-gray-900 px-2 py-1 text-xs font-bold text-white"
                            x-text="seconds"></span>
                    </div>

                    @if ($flashSale->label)
                        <span class="hidden text-sm font-medium text-gray-500 sm:inline">{{ $flashSale->label }}</span>
                    @endif
                </div>

                <a href="{{ route('products') }}?flash_sale={{ $flashSale->id }}"
                    class="flex items-center text-sm font-medium text-orange-600 hover:text-orange-700">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- Baris produk: scroll horizontal --}}
            <div class="overflow-x-auto pb-2" x-data="{}" style="scrollbar-width: thin;">
                <div class="flex gap-3">
                    @foreach ($flashSale->items()->limit(12)->get() as $item)
                        @php
                            $sold =
                                $item->stock_limit > 0
                                    ? round((($item->stock_limit - $item->remaining_stock) / $item->stock_limit) * 100)
                                    : 0;
                        @endphp
                        <a href="{{ route('product.show', $item->product_id) }}"
                            class="group flex-shrink-0 overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm transition-shadow hover:shadow-md"
                            style="width: 168px;">

                            {{-- Gambar + badge diskon --}}
                            <div class="relative">
                                <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                    class="h-[168px] w-full object-cover">

                                {{-- Label Mall/ORI --}}
                                <div class="absolute left-1.5 top-1.5 flex gap-1">
                                    @if ($item->is_mall ?? false)
                                        <span
                                            class="rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white">Mall</span>
                                    @endif
                                    @if ($item->is_original ?? true)
                                        <span
                                            class="rounded bg-orange-500 px-1.5 py-0.5 text-[10px] font-bold text-white">ORI</span>
                                    @endif
                                </div>

                                {{-- Badge persen diskon (pojok kanan atas, model "petir") --}}
                                <div class="absolute right-0 top-0">
                                    <div class="flex items-center gap-0.5 rounded-bl-lg bg-yellow-400 px-1.5 py-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-600"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" />
                                        </svg>
                                        <span
                                            class="text-xs font-extrabold text-red-600">-{{ $item->discount_percentage }}%</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info harga + stok --}}
                            <div class="p-2">
                                <p class="text-sm font-bold text-orange-600">
                                    Rp{{ number_format($item->sale_price, 0, ',', '.') }}
                                </p>

                                {{-- Progress bar stok terbatas --}}
                                <div class="relative mt-1.5 h-4 overflow-hidden rounded-full bg-orange-100">
                                    <div class="absolute inset-y-0 left-0 rounded-full"
                                        style="width: {{ max($sold, 8) }}%; background: linear-gradient(to right, rgb(251,146,60), rgb(239,68,68));">
                                    </div>
                                    <span
                                        class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-white">
                                        @if ($item->remaining_stock <= 0)
                                            HABIS
                                        @else
                                            STOK TERBATAS
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endif
