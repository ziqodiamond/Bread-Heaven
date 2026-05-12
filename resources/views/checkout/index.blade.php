<x-layout>

    {{--
    CHECKOUT PAGE
    - Data toko dari model Store (nama, alamat, jam operasional, maps)
    - Payment methods dari DB grouped by category + image_url support
    - Shipping methods via AJAX /checkout/shipping-rates (Biteship-ready)
    - Alpine x-data dengan window.__vars untuk menghindari CSP eval error
--}}

    <style>
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
            transition: color .25s;
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
        }

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

        .address-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1.5px solid #ebebeb;
            cursor: pointer;
            transition: border-color .2s, background .2s;
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
            transition: border-color .2s;
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

        .option-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #ebebeb;
            cursor: pointer;
            transition: border-color .2s;
        }

        .dark .option-card {
            border-color: #2a2a27;
        }

        .option-card:hover {
            border-color: #c8c8c8;
        }

        .dark .option-card:hover {
            border-color: #444;
        }

        .option-card.selected {
            border-color: #111;
        }

        .dark .option-card.selected {
            border-color: #e0ddd6;
        }

        .category-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #bbb;
            padding: 16px 0 8px;
        }

        .category-label:first-child {
            padding-top: 0;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f5f4f0;
            border: 1px solid #ebebeb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .dark .payment-icon {
            background: #1e1e1b;
            border-color: #2a2a27;
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

        .info-tile {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .dark .info-tile {
            background: #111;
            border-color: #2a2a27;
        }

        .note-textarea {
            width: 100%;
            resize: none;
            border: 1.5px solid #ebebeb;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 14px;
            color: #0f0f0f;
            background: transparent;
            outline: none;
            transition: border-color .2s;
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

        .submit-btn {
            width: 100%;
            padding: 15px;
            border-radius: 16px;
            background: #0f0f0f;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background .2s, transform .1s;
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

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e5e5e5;
            border-top-color: #888;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            flex-shrink: 0;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- ============================================================
         FIX CSP EVAL ERROR:
         Semua data PHP di-pass lewat window.__vars di <script> tag biasa,
         bukan di-embed langsung di dalam string atribut x-data="{ ... }".
         Alpine.js tidak perlu eval() untuk membaca property dari object JS.
    ============================================================ --}}
    @php
        $paymentFeesData = $paymentMethods
            ->mapWithKeys(
                fn($pm) => [
                    $pm->id => [
                        'fee_type' => $pm->fee_type,
                        'fee_value' => (float) $pm->fee_value,
                        'fee_tax_type' => $pm->fee_tax_type,
                    ],
                ],
            )
            ->toArray();

        $defaultAddressId = $addresses->where('is_default', true)->first()?->id;
    @endphp

    <script>
        window.__checkoutData = {
            paymentFees: @json($paymentFeesData),
            selectedAddress: @json($defaultAddressId),
            subtotal: {{ (int) $subtotal }},
            totalWeight: {{ (int) $totalWeight }},
        };
    </script>

    <div class="checkout-root mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8" x-data="{
    
        deliveryMode: 'delivery',
        selectedAddress: null,
        selectedShipping: null,
        shippingPrice: 0,
        shippingRates: [],
        loadingRates: false,
        selectedPayment: null,
        subtotal: 0,
        totalWeight: 0,
        paymentFees: {},
    
        get paymentFee() {
            if (!this.selectedPayment) return 0;
            const cfg = this.paymentFees[this.selectedPayment];
            if (!cfg) return 0;
            const base = this.subtotal + (this.deliveryMode === 'delivery' ? this.shippingPrice : 0);
            if (cfg.fee_type === 'fixed') {
                let fee = cfg.fee_value;
                if (cfg.fee_tax_type === 'before_tax') fee = fee * 1.11;
                return Math.round(fee);
            }
            if (cfg.fee_type === 'percent') {
                return Math.round(base * (cfg.fee_value / 100));
            }
            return 0;
        },
    
        calcFeeFor(pmId) {
            const cfg = this.paymentFees[pmId];
            if (!cfg) return 0;
            const base = this.subtotal + (this.deliveryMode === 'delivery' ? this.shippingPrice : 0);
            if (cfg.fee_type === 'fixed') {
                let fee = cfg.fee_value;
                if (cfg.fee_tax_type === 'before_tax') fee = fee * 1.11;
                return Math.round(fee);
            }
            if (cfg.fee_type === 'percent') {
                return Math.round(base * (cfg.fee_value / 100));
            }
            return 0;
        },
    
        feeLabel(pmId) {
            const cfg = this.paymentFees[pmId];
            if (!cfg) return '';
            const fee = this.calcFeeFor(pmId);
            if (fee === 0) return 'Gratis';
            if (cfg.fee_type === 'percent') {
                return '+' + cfg.fee_value + '% (' + this.formatRp(fee) + ')';
            }
            return '+' + this.formatRp(fee);
        },
    
        get total() {
            return this.subtotal + (this.deliveryMode === 'delivery' ? this.shippingPrice : 0) + this.paymentFee;
        },
    
        get canSubmit() {
            if (this.deliveryMode === 'pickup') return this.selectedPayment !== null;
            return this.selectedAddress !== null && this.selectedShipping !== null && this.selectedPayment !== null;
        },
    
        selectAddress(id) {
            this.selectedAddress = id;
            this.selectedShipping = null;
            this.shippingPrice = 0;
            this.shippingRates = [];
            this.fetchRates(id);
        },
    
        async fetchRates(addressId) {
            this.loadingRates = true;
            try {
                const res = await fetch(
                    '/checkout/shipping-rates?' + new URLSearchParams({
                        address_id: addressId,
                        weight: this.totalWeight,
                    }), {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                    }
                );
                const data = await res.json();
                console.log(data);
                if (!res.ok) throw data;
                this.shippingRates = data.rates ?? [];
            } catch (e) {
                console.error('DETAIL ERROR ONGKIR:', e);
                alert(e.error ?? e.message ?? 'Gagal fetch ongkir');
                this.shippingRates = [];
            } finally {
                this.loadingRates = false;
            }
        },
    
        selectShipping(rate) {
            this.selectedShipping = rate.id;
            this.shippingPrice = rate.price;
        },
    
        formatRp(val) {
            return 'Rp ' + Number(val).toLocaleString('id-ID');
        },
    
        init() {
            // Ambil semua data dari window.__checkoutData (tidak perlu eval)
            const d = window.__checkoutData ?? {};
            this.paymentFees = d.paymentFees ?? {};
            this.selectedAddress = d.selectedAddress ?? null;
            this.subtotal = d.subtotal ?? 0;
            this.totalWeight = d.totalWeight ?? 0;
    
            if (this.deliveryMode === 'delivery' && this.selectedAddress) {
                this.fetchRates(this.selectedAddress);
            }
        }
    }">

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <input type="hidden" name="delivery_mode" :value="deliveryMode">
            <input type="hidden" name="user_address_id" :value="selectedAddress">
            <input type="hidden" name="shipping_rate_id" :value="selectedShipping">
            <input type="hidden" name="payment_method_id" :value="selectedPayment">
            <input type="hidden" name="checkout_mode" value="{{ $checkoutMode }}">

            {{-- ========================= HEADER ========================= --}}
            <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        Checkout
                    </h1>
                    <p class="mt-2 text-sm text-gray-400">Selesaikan pesanan sebelum melakukan pembayaran</p>
                </div>

                @if ($store?->allow_pickup)
                    <div class="toggle-pill" style="min-width:260px;">
                        <div class="toggle-slider"
                            :style="deliveryMode === 'delivery'
                                ?
                                'left:4px; width:' + ($refs.optDelivery?.offsetWidth ?? 0) + 'px' :
                                'left:' + (($refs.optDelivery?.offsetWidth ?? 0) + 4) + 'px; width:' + ($refs.optPickup
                                    ?.offsetWidth ?? 0) + 'px'">
                        </div>
                        <span x-ref="optDelivery" class="toggle-option" :class="{ active: deliveryMode === 'delivery' }"
                            @click="deliveryMode = 'delivery'">🚚 Diantar</span>
                        <span x-ref="optPickup" class="toggle-option" :class="{ active: deliveryMode === 'pickup' }"
                            @click="deliveryMode = 'pickup'; selectedShipping = null; shippingPrice = 0;">🏪 Ambil
                            Sendiri</span>
                    </div>
                @endif
            </div>

            {{-- ========================= GRID ========================= --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- ============= KOLOM KIRI ============= --}}
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
                                                {{ $item->product_name }}</p>
                                            <p class="mt-1 text-xs text-gray-400">{{ $item->quantity }} pcs &middot;
                                                {{ number_format($item->total_weight / 1000, 2) }} kg</p>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Rp
                                            {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ============= DELIVERY MODE ============= --}}
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
                                <a href="{{ route('address.index') }}"
                                    class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">+
                                    Tambah</a>
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
                                            class="inline-flex rounded-xl bg-gray-900 px-5 py-2.5 text-xs font-medium text-white hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 transition-colors">Tambah
                                            Alamat</a>
                                    </div>
                                @else
                                    <div class="address-scroll">
                                        @foreach ($addresses as $address)
                                            <div class="address-card"
                                                :class="{ selected: selectedAddress === '{{ $address->id }}' }"
                                                @click="selectAddress('{{ $address->id }}')">
                                                <div class="radio-dot"
                                                    :class="{ checked: selectedAddress === '{{ $address->id }}' }">
                                                    <div class="radio-dot-inner"></div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $address->receiver_name }}</span>
                                                        @if ($address->is_default)
                                                            <span class="badge-utama">Utama</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-0.5 text-xs text-gray-400">
                                                        {{ $address->receiver_phone }}</p>
                                                    <p
                                                        class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                                        {{ $address->full_address_text }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 3 — SHIPPING --}}
                        <div class="mt-5 section-card" x-show="selectedAddress !== null" x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="section-header">
                                <div class="flex items-center gap-3">
                                    <div class="step-num">3</div>
                                    <h2>Metode Pengiriman</h2>
                                </div>
                                <span class="text-xs text-gray-400">{{ number_format($totalWeight / 1000, 2) }}
                                    kg</span>
                            </div>
                            <div class="p-6">

                                {{-- Loading --}}
                                <div x-show="loadingRates" class="flex items-center gap-3 py-4 text-sm text-gray-400">
                                    <div class="spinner"></div>
                                    <span>Mengambil ongkos kirim…</span>
                                </div>

                                {{-- Empty --}}
                                <div x-show="!loadingRates && shippingRates.length === 0"
                                    class="py-6 text-center text-sm text-gray-400">
                                    Tidak ada layanan pengiriman tersedia untuk alamat ini.
                                </div>

                                {{-- Rates --}}
                                <div x-show="!loadingRates && shippingRates.length > 0" class="space-y-3">
                                    <template x-for="rate in shippingRates" :key="rate.id">
                                        <div class="option-card" :class="{ selected: selectedShipping === rate.id }"
                                            @click="selectShipping(rate)">
                                            <div class="radio-dot" :class="{ checked: selectedShipping === rate.id }">
                                                <div class="radio-dot-inner"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                    x-text="rate.name"></p>
                                                <p class="text-xs text-gray-400 mt-0.5"
                                                    x-text="rate.etd ? 'Estimasi tiba ' + rate.etd : ''"></p>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap"
                                                x-text="formatRp(rate.price)"></span>
                                        </div>
                                    </template>
                                </div>

                            </div>
                        </div>
                    </div>{{-- end delivery --}}

                    {{-- ============= PICKUP MODE ============= --}}
                    @if ($store?->allow_pickup)
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
                                                <svg width="20" height="20" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5"
                                                    class="text-gray-500">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                    <polyline points="9 22 9 12 15 12 15 22" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $store->name }}</p>
                                                <p
                                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                                    {{ $store->full_address_text }}</p>
                                                @if ($store->phone)
                                                    <p class="mt-1 text-xs text-gray-400">{{ $store->phone }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($store->google_maps_url)
                                            <a href="{{ $store->google_maps_url }}" target="_blank" rel="noopener"
                                                class="mt-4 inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                <svg width="12" height="12" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 21s-8-5.686-8-11a8 8 0 1 1 16 0c0 5.314-8 11-8 11z" />
                                                    <circle cx="12" cy="10" r="2" />
                                                </svg>
                                                Lihat di Google Maps
                                            </a>
                                        @endif
                                        <p class="mt-3 text-xs text-gray-400 leading-relaxed">
                                            Tunjukkan bukti pembayaran dan nomor pesanan saat mengambil ke toko.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP — PEMBAYARAN --}}
                    <div class="section-card">
                        <div class="section-header">
                            <div class="flex items-center gap-3">
                                <div class="step-num" x-text="deliveryMode === 'pickup' ? '3' : '4'"></div>
                                <h2>Metode Pembayaran</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            @php
                                $groupedPayments = $paymentMethods->groupBy('category');
                                $categoryLabels = [
                                    'bank_transfer' => 'Transfer Bank',
                                    'ewallet' => 'E-Wallet',
                                    'qris' => 'QRIS',
                                    'cod' => 'Bayar di Tempat',
                                    'manual' => 'Transfer Manual',
                                ];
                            @endphp

                            @foreach ($groupedPayments as $category => $methods)
                                <p class="category-label">
                                    {{ $categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                                </p>
                                <div class="space-y-2 mb-2">
                                    @foreach ($methods as $pm)
                                        <div class="option-card"
                                            :class="{ selected: selectedPayment === '{{ $pm->id }}' }"
                                            @click="selectedPayment = '{{ $pm->id }}'">
                                            <div class="radio-dot"
                                                :class="{ checked: selectedPayment === '{{ $pm->id }}' }">
                                                <div class="radio-dot-inner"></div>
                                            </div>
                                            <div class="payment-icon">
                                                @if ($pm->image_url)
                                                    <img src="{{ $pm->image_url }}" alt="{{ $pm->name }}"
                                                        style="width:28px;height:28px;object-fit:contain;">
                                                @else
                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="1.8"
                                                        class="text-gray-400">
                                                        <rect x="1" y="4" width="22" height="16"
                                                            rx="2" ry="2" />
                                                        <line x1="1" y1="10" x2="23"
                                                            y2="10" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $pm->name }}</p>
                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    {{ ucfirst($pm->provider) }}
                                                    @if ($pm->account_number)
                                                        &middot; {{ $pm->account_number }}
                                                    @endif
                                                </p>
                                            </div>
                                            {{-- Fee label dihitung realtime oleh Alpine --}}
                                            <span class="text-xs font-medium whitespace-nowrap"
                                                :class="calcFeeFor('{{ $pm->id }}') === 0 ?
                                                    'text-green-600 dark:text-green-400' : 'text-amber-500'"
                                                x-text="feeLabel('{{ $pm->id }}')"></span>
                                        </div>
                                    @endforeach
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

                {{-- ============= SUMMARY ============= --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24 section-card">
                        <div class="section-header">
                            <h2>Ringkasan</h2>
                        </div>
                        <div class="p-6">

                            <div class="space-y-3 mb-5">
                                @foreach ($cart->items as $item)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}"
                                            style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;border:1px solid #f0efe9;">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-700 dark:text-gray-300 truncate">
                                                {{ $item->product_name }}</p>
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
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Berat</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($totalWeight / 1000, 2) }}
                                    kg</span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-400">Ongkir</span>
                                <span class="text-sm font-medium">
                                    <span x-show="deliveryMode === 'pickup'"
                                        class="text-green-600 dark:text-green-400">Gratis</span>
                                    <span x-show="deliveryMode === 'delivery' && shippingPrice === 0"
                                        class="text-gray-400">Belum dipilih</span>
                                    <span x-show="deliveryMode === 'delivery' && shippingPrice > 0"
                                        class="text-gray-900 dark:text-white" x-text="formatRp(shippingPrice)"></span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between mb-3" x-show="paymentFee > 0">
                                <span class="text-sm text-gray-400">Biaya Layanan</span>
                                <span class="text-sm font-medium text-amber-500" x-text="formatRp(paymentFee)"></span>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="flex items-baseline justify-between mb-6">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="text-2xl font-semibold text-gray-900 dark:text-white"
                                    style="font-family:'DM Serif Display',serif; letter-spacing:-.02em;"
                                    x-text="formatRp(total)"></span>
                            </div>

                            <button type="submit" class="submit-btn" :disabled="!canSubmit">
                                <span x-show="deliveryMode === 'delivery'">Bayar Sekarang →</span>
                                <span x-show="deliveryMode === 'pickup'">Lanjut Bayar →</span>
                            </button>

                            <p class="mt-3 text-center text-xs text-gray-400 min-h-[18px]" x-show="!canSubmit">
                                <span x-show="deliveryMode === 'delivery' && selectedAddress === null">Pilih alamat
                                    pengiriman dulu</span>
                                <span
                                    x-show="deliveryMode === 'delivery' && selectedAddress !== null && loadingRates">Menunggu
                                    data ongkir…</span>
                                <span
                                    x-show="deliveryMode === 'delivery' && selectedAddress !== null && !loadingRates && selectedShipping === null && shippingRates.length > 0">Pilih
                                    metode pengiriman</span>
                                <span
                                    x-show="(deliveryMode === 'delivery' && selectedShipping !== null || deliveryMode === 'pickup') && selectedPayment === null">Pilih
                                    metode pembayaran</span>
                            </p>

                        </div>
                    </div>
                </div>

            </div>{{-- end grid --}}
        </form>

    </div>

    {{-- Route yang perlu ditambahkan di web.php:
    GET  /checkout/shipping-rates   → [CheckoutController::class, 'shippingRates']
--}}

</x-layout>
