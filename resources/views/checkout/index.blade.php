<x-layout>

    {{--
    CHECKOUT PAGE — FIXED
    Root cause: @push('alpine') dirender di <head> sebelum Alpine.start()
    Fix: Hapus Alpine.data(), pakai x-data inline langsung di elemen.
    Alpine sudah ada saat element di-parse → tidak ada ReferenceError.
--}}

    <style>
        /* ===== TOGGLE PILL ===== */
        .toggle-pill {
            position: relative;
            display: inline-flex;
            background: #f1f0ec;
            border-radius: 999px;
            padding: 4px;
        }

        .dark .toggle-pill {
            background: #1e1e1b;
        }

        .toggle-option {
            position: relative;
            z-index: 1;
            padding: 10px 24px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.25s ease;
            color: #888;
            user-select: none;
        }

        .toggle-option.active {
            color: #0f0f0f;
        }

        .dark .toggle-option.active {
            color: #fafaf8;
        }

        .toggle-slider {
            position: absolute;
            top: 4px;
            height: calc(100% - 8px);
            background: #fff;
            border-radius: 999px;
            transition: left .3s cubic-bezier(.4, 0, .2, 1), width .3s cubic-bezier(.4, 0, .2, 1);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .10);
            pointer-events: none;
        }

        .dark .toggle-slider {
            background: #2a2a27;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .4);
        }

        /* ===== ADDRESS SCROLL ===== */
        .address-scroll {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 340px;
            overflow-y: auto;
            padding-right: 4px;
            scrollbar-width: thin;
            scrollbar-color: #ddd transparent;
        }

        .address-scroll::-webkit-scrollbar {
            width: 3px;
        }

        .address-scroll::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 99px;
        }

        /* ===== ADDRESS CARD ===== */
        .address-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1.5px solid #ebebeb;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease;
        }

        .dark .address-card {
            border-color: #2a2a27;
        }

        .address-card:hover {
            border-color: #c8c8c8;
        }

        .dark .address-card:hover {
            border-color: #444;
        }

        .address-card.selected {
            border-color: #111;
            background: #fafaf8;
        }

        .dark .address-card.selected {
            border-color: #e0ddd6;
            background: #1e1e1b;
        }

        /* ===== RADIO DOT ===== */
        .radio-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            transition: border-color .2s ease;
        }

        .radio-dot.checked {
            border-color: #111;
        }

        .dark .radio-dot.checked {
            border-color: #e0ddd6;
        }

        .radio-dot-inner {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #111;
            transform: scale(0);
            transition: transform .2s cubic-bezier(.4, 0, .2, 1);
        }

        .dark .radio-dot-inner {
            background: #e0ddd6;
        }

        .radio-dot.checked .radio-dot-inner {
            transform: scale(1);
        }

        /* ===== SECTION CARD ===== */
        .section-card {
            border-radius: 20px;
            border: 1px solid #ebebeb;
            background: #fff;
            overflow: hidden;
        }

        .dark .section-card {
            border-color: #222;
            background: #111;
        }

        .section-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f0efe9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dark .section-header {
            border-color: #1e1e1b;
        }

        .section-header h2 {
            font-size: 14px;
            font-weight: 600;
            color: #0f0f0f;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dark .section-header h2 {
            color: #fafaf8;
        }

        /* ===== STEP NUM ===== */
        .step-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #0f0f0f;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .dark .step-num {
            background: #fafaf8;
            color: #111;
        }

        /* ===== SHIPPING / PAYMENT CARD ===== */
        .shipping-card,
        .payment-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #ebebeb;
            cursor: pointer;
            transition: border-color .2s ease;
        }

        .dark .shipping-card,
        .dark .payment-card {
            border-color: #2a2a27;
        }

        .shipping-card.selected,
        .payment-card.selected {
            border-color: #111;
        }

        .dark .shipping-card.selected,
        .dark .payment-card.selected {
            border-color: #e0ddd6;
        }

        /* ===== NOTE TEXTAREA ===== */
        .note-textarea {
            width: 100%;
            resize: none;
            border: 1.5px solid #ebebeb;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #0f0f0f;
            background: transparent;
            outline: none;
            transition: border-color .2s ease;
            line-height: 1.6;
        }

        .dark .note-textarea {
            border-color: #2a2a27;
            color: #fafaf8;
        }

        .note-textarea:focus {
            border-color: #111;
        }

        .dark .note-textarea:focus {
            border-color: #e0ddd6;
        }

        .note-textarea::placeholder {
            color: #bbb;
        }

        /* ===== SUBMIT BTN ===== */
        .submit-btn {
            width: 100%;
            padding: 15px;
            border-radius: 16px;
            background: #0f0f0f;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: -.01em;
            border: none;
            cursor: pointer;
            transition: background .2s ease, transform .1s ease;
        }

        .dark .submit-btn {
            background: #fafaf8;
            color: #111;
        }

        .submit-btn:hover:not(:disabled) {
            background: #1e1e1b;
        }

        .dark .submit-btn:hover:not(:disabled) {
            background: #e5e2da;
        }

        .submit-btn:active:not(:disabled) {
            transform: scale(.98);
        }

        .submit-btn:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        /* ===== MISC ===== */
        .summary-divider {
            height: 1px;
            background: #f0efe9;
            margin: 16px 0;
        }

        .dark .summary-divider {
            background: #1e1e1b;
        }

        .badge-utama {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 99px;
            background: #f0efe9;
            color: #555;
        }

        .dark .badge-utama {
            background: #2a2a27;
            color: #aaa;
        }

        .pickup-info {
            border-radius: 14px;
            background: #fafaf8;
            border: 1px solid #ebebeb;
            padding: 20px;
        }

        .dark .pickup-info {
            background: #1a1a17;
            border-color: #2a2a27;
        }

        .product-img {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #f0efe9;
            flex-shrink: 0;
        }

        .dark .product-img {
            border-color: #2a2a27;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    {{--
    ====================================================================
    FIX: x-data INLINE — tidak perlu Alpine.data() / @push('alpine')
    Data langsung dideklarasikan di sini, Alpine baca saat DOM ready.
    ====================================================================
--}}
    <div class="checkout-root mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8" x-data="{
        deliveryMode: 'delivery',
        selectedAddress: {{ json_encode($addresses->where('is_default', true)->first()?->id) ?? 'null' }},
        selectedShipping: null,
        selectedPayment: null,
        shippingPrice: 0,
        subtotal: {{ (int) $subtotal }},
        get total() {
            return this.subtotal + (this.deliveryMode === 'delivery' ? this.shippingPrice : 0);
        },
        get canSubmit() {
            if (this.deliveryMode === 'pickup') {
                return this.selectedPayment !== null;
            }
            return this.selectedAddress !== null &&
                this.selectedShipping !== null &&
                this.selectedPayment !== null;
        }
    }">

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            {{-- Hidden fields — diisi Alpine secara reaktif --}}
            <input type="hidden" name="delivery_mode" :value="deliveryMode">
            <input type="hidden" name="user_address_id" :value="selectedAddress">
            <input type="hidden" name="shipping_option_id" :value="selectedShipping">
            <input type="hidden" name="payment_method_id" :value="selectedPayment">

            {{-- ========================= HEADER ========================= --}}
            <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <h1 class="text-4xl font-normal text-gray-900 dark:text-white"
                        style="font-family:'DM Serif Display',serif; letter-spacing:-.02em;">
                        Checkout
                    </h1>
                    <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">
                        Selesaikan pesanan sebelum melakukan pembayaran
                    </p>
                </div>

                {{-- ========================= TOGGLE ========================= --}}
                <div class="toggle-pill" style="min-width:260px;">
                    <div class="toggle-slider"
                        :style="deliveryMode === 'delivery'
                            ?
                            'left:4px; width:' + ($refs.optDelivery?.offsetWidth ?? 0) + 'px' :
                            'left:' + (($refs.optDelivery?.offsetWidth ?? 0) + 4) + 'px; width:' + ($refs.optPickup
                                ?.offsetWidth ?? 0) + 'px'">
                    </div>
                    <span x-ref="optDelivery" class="toggle-option" :class="{ active: deliveryMode === 'delivery' }"
                        @click="deliveryMode = 'delivery'">
                        🚚 Diantar
                    </span>
                    <span x-ref="optPickup" class="toggle-option" :class="{ active: deliveryMode === 'pickup' }"
                        @click="deliveryMode = 'pickup'">
                        🏪 Ambil Sendiri
                    </span>
                </div>

            </div>

            {{-- ========================= GRID ========================= --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- ===================== KOLOM KIRI ===================== --}}
                <div class="space-y-5 lg:col-span-2">

                    {{-- STEP 1 — PRODUK --}}
                    <div class="section-card">
                        <div class="section-header">
                            <div class="flex items-center gap-3">
                                <div class="step-num">1</div>
                                <h2>Produk Pesanan</h2>
                            </div>
                            <span class="text-sm text-gray-400">{{ $cart->items->count() }} item</span>
                        </div>
                        <div class="divide-y divide-gray-50 dark:divide-gray-800/60 px-6">
                            @foreach ($cart->items as $item)
                                <div class="flex gap-4 py-5">
                                    <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                        class="product-img">
                                    <div class="flex flex-1 flex-col justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white leading-snug">
                                                {{ $item->product_name }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $item->quantity }} pcs &middot;
                                                {{ number_format($item->total_weight / 1000, 2) }} kg
                                            </p>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ===================== MODE DELIVERY ===================== --}}
                    <div x-show="deliveryMode === 'delivery'" x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2">

                        {{-- STEP 2 — ALAMAT --}}
                        <div class="section-card">
                            <div class="section-header">
                                <div class="flex items-center gap-3">
                                    <div class="step-num">2</div>
                                    <h2>Alamat Pengiriman</h2>
                                </div>
                                <a href="{{ route('profile.edit') }}"
                                    class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                    + Tambah
                                </a>
                            </div>

                            <div class="p-6">
                                @if ($addresses->isEmpty())
                                    <div class="flex flex-col items-center gap-3 py-8 text-center">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.2" class="text-gray-300">
                                            <path d="M12 21s-8-5.686-8-11a8 8 0 1 1 16 0c0 5.314-8 11-8 11z" />
                                            <circle cx="12" cy="10" r="2" />
                                        </svg>
                                        <p class="text-sm text-gray-400">Belum ada alamat pengiriman</p>
                                        <a href="{{ route('profile.edit') }}"
                                            class="mt-1 inline-flex rounded-xl bg-gray-900 px-5 py-2.5 text-xs font-medium text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 transition-colors">
                                            Tambah Alamat
                                        </a>
                                    </div>
                                @else
                                    <div class="address-scroll">
                                        @foreach ($addresses as $address)
                                            <div class="address-card"
                                                :class="{ selected: selectedAddress === '{{ $address->id }}' }"
                                                @click="selectedAddress = '{{ $address->id }}'; selectedShipping = null; shippingPrice = 0;">

                                                <div class="radio-dot"
                                                    :class="{ checked: selectedAddress === '{{ $address->id }}' }">
                                                    <div class="radio-dot-inner"></div>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $address->receiver_name }}
                                                        </span>
                                                        @if ($address->is_default)
                                                            <span class="badge-utama">Utama</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-0.5 text-xs text-gray-400">
                                                        {{ $address->receiver_phone }}
                                                    </p>
                                                    <p
                                                        class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                                        {{ $address->full_address_text }}
                                                    </p>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 3 — PENGIRIMAN (muncul setelah alamat dipilih) --}}
                        <div class="mt-5 section-card" x-show="selectedAddress !== null" x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">

                            <div class="section-header">
                                <div class="flex items-center gap-3">
                                    <div class="step-num">3</div>
                                    <h2>Metode Pengiriman</h2>
                                </div>
                            </div>

                            <div class="p-6">
                                @php
                                    $dummyShipping = [
                                        [
                                            'id' => 'jne_reg',
                                            'name' => 'JNE Regular',
                                            'etd' => '2-3 hari',
                                            'price' => 18000,
                                        ],
                                        [
                                            'id' => 'jne_yes',
                                            'name' => 'JNE YES (Next Day)',
                                            'etd' => '1 hari',
                                            'price' => 35000,
                                        ],
                                        [
                                            'id' => 'sicepat_reg',
                                            'name' => 'SiCepat Regular',
                                            'etd' => '2-3 hari',
                                            'price' => 15000,
                                        ],
                                    ];
                                @endphp
                                <div class="space-y-3">
                                    @foreach ($dummyShipping as $opt)
                                        <div class="shipping-card"
                                            :class="{ selected: selectedShipping === '{{ $opt['id'] }}' }"
                                            @click="selectedShipping = '{{ $opt['id'] }}'; shippingPrice = {{ $opt['price'] }}">

                                            <div class="radio-dot"
                                                :class="{ checked: selectedShipping === '{{ $opt['id'] }}' }">
                                                <div class="radio-dot-inner"></div>
                                            </div>

                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $opt['name'] }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    Estimasi tiba {{ $opt['etd'] }}
                                                </p>
                                            </div>

                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                Rp {{ number_format($opt['price'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>{{-- end delivery mode --}}

                    {{-- ===================== MODE PICKUP ===================== --}}
                    <div x-show="deliveryMode === 'pickup'" x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2">

                        <div class="section-card">
                            <div class="section-header">
                                <div class="flex items-center gap-3">
                                    <div class="step-num">2</div>
                                    <h2>Info Pengambilan</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="pickup-info">
                                    <div class="flex items-start gap-4">
                                        <div style="width:40px;height:40px;border-radius:12px;background:#f0efe9;display:flex;align-items:center;justify-content:center;flex-shrink:0;"
                                            class="dark:bg-gray-800">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5" class="text-gray-500">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                <polyline points="9 22 9 12 15 12 15 22" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Toko Utama</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                                Jl. Contoh No. 12, Bandung, Jawa Barat 40123
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-5 grid grid-cols-2 gap-3">
                                        <div style="background:#fff;border:1px solid #ebebeb;border-radius:12px;padding:12px 14px;"
                                            class="dark:bg-gray-900 dark:border-gray-700">
                                            <p class="text-xs text-gray-400 mb-1">Jam Operasional</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">08.00 –
                                                21.00 WIB</p>
                                        </div>
                                        <div style="background:#fff;border:1px solid #ebebeb;border-radius:12px;padding:12px 14px;"
                                            class="dark:bg-gray-900 dark:border-gray-700">
                                            <p class="text-xs text-gray-400 mb-1">Siap Diambil</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">± 30 menit
                                                setelah bayar</p>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-xs text-gray-400 leading-relaxed">
                                        Tunjukkan bukti pembayaran dan nomor pesanan saat mengambil ke toko.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end pickup mode --}}

                    {{-- STEP — PEMBAYARAN --}}
                    <div class="section-card">
                        <div class="section-header">
                            <div class="flex items-center gap-3">
                                <div class="step-num" x-text="deliveryMode === 'pickup' ? '3' : '4'"></div>
                                <h2>Metode Pembayaran</h2>
                            </div>
                        </div>
                        <div class="space-y-3 p-6">
                            @foreach ($paymentMethods as $pm)
                                <div class="payment-card"
                                    :class="{ selected: selectedPayment == '{{ $pm->id }}' }"
                                    @click="selectedPayment = '{{ $pm->id }}'">

                                    <div class="radio-dot"
                                        :class="{ checked: selectedPayment == '{{ $pm->id }}' }">
                                        <div class="radio-dot-inner"></div>
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $pm->name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-400">
                                            {{ ucfirst($pm->provider) }}
                                        </p>
                                    </div>

                                    <div style="width:32px;height:32px;border-radius:8px;background:#f0efe9;display:flex;align-items:center;justify-content:center;"
                                        class="dark:bg-gray-800">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.8" class="text-gray-500">
                                            <rect x="1" y="4" width="22" height="16" rx="2"
                                                ry="2" />
                                            <line x1="1" y1="10" x2="23" y2="10" />
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="section-card">
                        <div class="section-header">
                            <div class="flex items-center gap-3">
                                <div class="step-num" x-text="deliveryMode === 'pickup' ? '4' : '5'"></div>
                                <h2>Catatan</h2>
                            </div>
                            <span class="text-xs text-gray-400">Opsional</span>
                        </div>
                        <div class="p-6">
                            <textarea name="notes" rows="3" placeholder="Contoh: Tolong packing lebih aman, hindari banting"
                                class="note-textarea"></textarea>
                        </div>
                    </div>

                </div>{{-- end kolom kiri --}}

                {{-- ===================== KOLOM KANAN — SUMMARY ===================== --}}
                <div class="lg:col-span-1">

                    <div class="sticky top-24 section-card">

                        <div class="section-header">
                            <h2>Ringkasan</h2>
                        </div>

                        <div class="p-6">

                            {{-- Item ringkas --}}
                            <div class="space-y-3 mb-5">
                                @foreach ($cart->items as $item)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                            style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;border:1px solid #f0efe9;">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-700 dark:text-gray-300 truncate">
                                                {{ $item->product_name }}
                                            </p>
                                            <p class="text-xs text-gray-400">×{{ $item->quantity }}</p>
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="summary-divider"></div>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Subtotal</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Total Berat</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($totalWeight / 1000, 2) }} kg
                                </span>
                            </div>

                            {{-- Ongkir delivery --}}
                            <div x-show="deliveryMode === 'delivery'" class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Ongkir</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    <span x-show="shippingPrice === 0" class="text-gray-400">Belum dipilih</span>
                                    <span x-show="shippingPrice > 0"
                                        x-text="'Rp ' + shippingPrice.toLocaleString('id-ID')"></span>
                                </span>
                            </div>

                            {{-- Ongkir pickup --}}
                            <div x-show="deliveryMode === 'pickup'" class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Ongkir</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">Gratis</span>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="flex items-baseline justify-between mb-6">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="text-2xl font-semibold text-gray-900 dark:text-white"
                                    style="font-family:'DM Serif Display',serif; letter-spacing:-.02em;"
                                    x-text="'Rp ' + total.toLocaleString('id-ID')">
                                </span>
                            </div>

                            <button type="submit" class="submit-btn" :disabled="!canSubmit">
                                <span x-show="deliveryMode === 'delivery'">Bayar Sekarang →</span>
                                <span x-show="deliveryMode === 'pickup'">Lanjut Bayar →</span>
                            </button>

                            {{-- Helper text --}}
                            <div class="mt-3 text-center text-xs text-gray-400 min-h-[18px]" x-show="!canSubmit">
                                <span x-show="deliveryMode === 'delivery' && selectedAddress === null">
                                    Pilih alamat pengiriman dulu
                                </span>
                                <span
                                    x-show="deliveryMode === 'delivery' && selectedAddress !== null && selectedShipping === null">
                                    Pilih metode pengiriman
                                </span>
                                <span
                                    x-show="deliveryMode === 'delivery' && selectedShipping !== null && selectedPayment === null">
                                    Pilih metode pembayaran
                                </span>
                                <span x-show="deliveryMode === 'pickup' && selectedPayment === null">
                                    Pilih metode pembayaran
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>{{-- end grid --}}

        </form>

    </div>

    {{-- TIDAK ADA @push('alpine') — tidak diperlukan lagi --}}

</x-layout>
