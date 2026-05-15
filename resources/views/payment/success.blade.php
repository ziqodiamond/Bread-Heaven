{{-- resources/views/payment/success.blade.php --}}

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Geist', sans-serif;
        }

        /* Animasi kustom — tidak bisa diganti Tailwind utility */
        @keyframes sp-pop {
            from {
                transform: scale(.5);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes sp-up {
            from {
                transform: translateY(10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes sp-fade {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-pop {
            animation: sp-pop .5s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        .anim-up {
            animation: sp-up .55s ease both;
        }

        .anim-fade {
            animation: sp-fade .4s ease both;
        }

        /* Delay helper — Tailwind v4 pakai arbitrary value [Xs] tapi tetap perlu helper ini */
        .delay-0 {
            animation-delay: 0s;
        }

        .delay-15 {
            animation-delay: .15s;
        }

        .delay-25 {
            animation-delay: .25s;
        }

        .delay-35 {
            animation-delay: .35s;
        }

        .delay-42 {
            animation-delay: .42s;
        }

        .delay-1 {
            animation-delay: .10s;
        }

        .delay-18 {
            animation-delay: .18s;
        }

        .delay-26 {
            animation-delay: .26s;
        }

        .delay-34 {
            animation-delay: .34s;
        }

        /* Garis dekoratif hero — pseudo element, tidak bisa pure Tailwind */
        .hero-line::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .12), transparent);
        }

        /* Font serif untuk judul */
        .font-serif-display {
            font-family: 'Instrument Serif', serif;
        }
    </style>
@endpush

<x-layout>
    {{-- Root --}}
    <div class="min-h-screen bg-[#fafaf9] text-[#1c1c1a]">

        {{-- ── Hero ── --}}
        <div class="hero-line bg-[#111] px-6 pt-[52px] pb-12 text-center relative overflow-hidden">

            {{-- Checkmark circle --}}
            <div
                class="anim-pop delay-0 w-14 h-14 rounded-full border border-white/25 inline-flex items-center justify-center mb-5">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="anim-up delay-15 font-serif-display text-[2rem] text-white m-0 mb-1.5">
                Pembayaran Berhasil
            </h1>
            <p class="anim-up delay-25 text-[.8125rem] text-white/45 font-light tracking-[.01em]">
                Pesananmu dikonfirmasi &amp; sedang diproses
            </p>

            {{-- Invoice badge --}}
            <div
                class="anim-up delay-35 inline-flex items-center gap-1.5 mt-[18px] px-3.5 py-1.5 border border-white/[.12] rounded-full text-[.75rem] text-white/55 font-normal tracking-[.02em]">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ $order->invoice_number }}
            </div>
        </div>

        {{-- ── Body ── --}}
        <div class="max-w-[440px] mx-auto px-4 pt-6 pb-10 flex flex-col gap-2.5">

            {{-- ── Card: Status Pesanan ── --}}
            <div class="anim-fade delay-1 bg-white border border-[#ebebea] rounded-[14px] p-5">
                <p class="text-[.6875rem] font-medium tracking-[.07em] uppercase text-[#b0afa9] mb-3.5">
                    Status Pesanan
                </p>

                {{-- Progress steps --}}
                <div class="flex items-center">

                    {{-- Step 1: Dibayar --}}
                    <div class="flex items-center gap-1.5 text-[.75rem] font-medium text-[#1c1c1a] whitespace-nowrap">
                        <div
                            class="w-5 h-5 rounded-full text-[.625rem] font-semibold flex items-center justify-center shrink-0 bg-[#1c1c1a] border-[1.5px] border-[#1c1c1a] text-white">
                            ✓
                        </div>
                        <span>Dibayar</span>
                    </div>

                    <div class="flex-1 h-px bg-[#1c1c1a] mx-1.5 max-w-9"></div>

                    {{-- Step 2: Diproses --}}
                    <div class="flex items-center gap-1.5 text-[.75rem] font-medium text-[#1c1c1a] whitespace-nowrap">
                        <div
                            class="w-5 h-5 rounded-full text-[.625rem] font-semibold flex items-center justify-center shrink-0 bg-white border-[1.5px] border-[#1c1c1a] text-[#1c1c1a] shadow-[0_0_0_3px_rgba(28,28,26,.08)]">
                            2
                        </div>
                        <span>Diproses</span>
                    </div>

                    <div class="flex-1 h-px bg-[#e0e0dd] mx-1.5 max-w-9"></div>

                    {{-- Step 3: Dikirim --}}
                    <div class="flex items-center gap-1.5 text-[.75rem] text-[#b0afa9] whitespace-nowrap">
                        <div
                            class="w-5 h-5 rounded-full text-[.625rem] font-semibold flex items-center justify-center shrink-0 bg-white border-[1.5px] border-[#e0e0dd] text-[#b0afa9]">
                            3
                        </div>
                        <span>Dikirim</span>
                    </div>

                    <div class="flex-1 h-px bg-[#e0e0dd] mx-1.5 max-w-9"></div>

                    {{-- Step 4: Selesai --}}
                    <div class="flex items-center gap-1.5 text-[.75rem] text-[#b0afa9] whitespace-nowrap">
                        <div
                            class="w-5 h-5 rounded-full text-[.625rem] font-semibold flex items-center justify-center shrink-0 bg-white border-[1.5px] border-[#e0e0dd] text-[#b0afa9]">
                            4
                        </div>
                        <span>Selesai</span>
                    </div>

                </div>
            </div>

            {{-- ── Card: Detail Pembayaran ── --}}
            <div class="anim-fade delay-18 bg-white border border-[#ebebea] rounded-[14px] p-5">
                <p class="text-[.6875rem] font-medium tracking-[.07em] uppercase text-[#b0afa9] mb-3.5">
                    Detail Pembayaran
                </p>

                {{-- Status --}}
                <div class="flex justify-between items-center py-2 border-b border-[#f3f3f1] text-[.8125rem]">
                    <span class="text-[#7a7973]">Status</span>
                    @if ($order->payment_status === 'paid')
                        <span
                            class="text-[.7rem] font-medium px-2.5 py-[3px] rounded-full inline-flex items-center gap-1 bg-[#f0faf4] text-[#0d6838] border border-[#c6ecd8]">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Lunas
                        </span>
                    @else
                        <span
                            class="text-[.7rem] font-medium px-2.5 py-[3px] rounded-full bg-[#fffbeb] text-[#92400e] border border-[#fde68a]">
                            Pending
                        </span>
                    @endif
                </div>

                {{-- Metode --}}
                @if ($transaction?->payment_type)
                    <div class="flex justify-between items-center py-2 border-b border-[#f3f3f1] text-[.8125rem]">
                        <span class="text-[#7a7973]">Metode</span>
                        <span class="font-medium text-[#1c1c1a]">
                            {{ ucwords(str_replace('_', ' ', $transaction->payment_type)) }}
                        </span>
                    </div>
                @endif

                {{-- Waktu Bayar --}}
                @if ($transaction?->paid_at)
                    <div class="flex justify-between items-center py-2 border-b border-[#f3f3f1] text-[.8125rem]">
                        <span class="text-[#7a7973]">Waktu Bayar</span>
                        <span class="font-medium text-[#1c1c1a]">
                            {{ $transaction->paid_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                @endif

                {{-- Total --}}
                <div class="flex justify-between items-baseline pt-3.5 mt-1 border-t border-[#ebebea]">
                    <span class="text-[.8125rem] font-medium text-[#1c1c1a]">Total</span>
                    <span class="text-[1.25rem] font-semibold text-[#1c1c1a] tracking-[-0.02em]">
                        Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- ── Card: Item Pesanan ── --}}
            <div class="anim-fade delay-26 bg-white border border-[#ebebea] rounded-[14px] p-5">
                <p class="text-[.6875rem] font-medium tracking-[.07em] uppercase text-[#b0afa9] mb-3.5">
                    Item Pesanan &nbsp;·&nbsp; {{ $order->items->count() }}
                </p>

                @foreach ($order->items as $index => $item)
                    <div class="anim-fade flex items-center gap-3 py-2.5 border-b border-[#f3f3f1] last:border-b-0"
                        style="animation-delay: {{ 0.1 + $index * 0.06 }}s">

                        {{-- Icon box --}}
                        <div class="w-[38px] h-[38px] rounded-[10px] bg-[#f5f5f3] overflow-hidden shrink-0">

                            <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[.8125rem] font-medium text-[#1c1c1a] leading-[1.3]">
                                {{ $item->product_name }}
                            </p>
                            <p class="text-[.75rem] text-[#a0a09a] mt-[1px]">
                                {{ $item->quantity }}× Rp {{ number_format($item->product_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <span class="text-[.8125rem] font-medium text-[#1c1c1a] shrink-0">
                            Rp {{ number_format($item->product_price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach

                {{-- Rincian Harga --}}
                <div class="mt-3 pt-3 border-t border-[#f3f3f1]">

                    <div class="flex justify-between text-[.75rem] text-[#7a7973] mb-[5px]">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if ($order->shipping_cost > 0)
                        <div class="flex justify-between text-[.75rem] text-[#7a7973] mb-[5px]">
                            <span>Ongkir ({{ $order->shipping_courier }})</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if ($order->service_fee > 0)
                        <div class="flex justify-between text-[.75rem] text-[#7a7973] mb-[5px]">
                            <span>Biaya Layanan</span>
                            <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-[.75rem] text-[#0d6838] mb-[5px]">
                            <span>Diskon</span>
                            <span>− Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ── Card: Alamat Pengiriman ── --}}
            <div class="anim-fade delay-34 bg-white border border-[#ebebea] rounded-[14px] p-5">
                <p class="text-[.6875rem] font-medium tracking-[.07em] uppercase text-[#b0afa9] mb-3.5">
                    Dikirim ke
                </p>

                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div
                        class="w-[34px] h-[34px] rounded-full bg-[#f5f5f3] flex items-center justify-center shrink-0 mt-[1px] text-[#7a7973]">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-[.8125rem] font-medium text-[#1c1c1a]">
                            {{ $order->shipping_receiver_name }}
                        </p>
                        <p class="text-[.75rem] text-[#a0a09a] mt-0.5">
                            {{ $order->shipping_receiver_phone }}
                        </p>
                        <p class="text-[.75rem] text-[#7a7973] mt-1 leading-relaxed">
                            {{ $order->shipping_full_address }}, {{ $order->shipping_city }}
                            @if ($order->shipping_postal_code)
                                {{ $order->shipping_postal_code }}
                            @endif
                        </p>

                        @if ($order->shipping_courier)
                            <span
                                class="inline-flex items-center gap-1 mt-2 px-2 py-[3px] bg-[#f5f5f3] rounded-full text-[.7rem] text-[#7a7973] font-medium tracking-[.02em]">
                                {{ strtoupper($order->shipping_courier) }}
                                @if ($order->shipping_service)
                                    · {{ $order->shipping_service }}
                                @endif
                                @if ($order->shipping_etd)
                                    · {{ $order->shipping_etd }} hari
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Action Buttons ── --}}
            <div class="anim-fade delay-42 flex flex-col gap-2">

                <a href="{{ route('orders.show', $order->id) }}"
                    class="block w-full py-3.5 bg-[#1c1c1a] text-white rounded-xl text-center text-[.8125rem] font-medium no-underline tracking-[.01em] transition-all duration-200 hover:opacity-80 hover:-translate-y-px">
                    Lihat Detail Pesanan
                </a>

                <a href="{{ route('orders.history') }}"
                    class="block w-full py-[13px] border-[1.5px] border-[#deded9] text-[#1c1c1a] rounded-xl text-center text-[.8125rem] font-medium no-underline transition-all duration-200 hover:border-[#1c1c1a] hover:-translate-y-px">
                    Riwayat Pesanan
                </a>

                <a href="{{ route('home') }}"
                    class="block text-center text-[.8rem] text-[#a0a09a] py-2 no-underline transition-colors duration-200 hover:text-[#1c1c1a]">
                    Lanjut Belanja →
                </a>

            </div>

        </div>
    </div>

</x-layout>
