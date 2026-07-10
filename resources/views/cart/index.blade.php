<x-layout>
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">

        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">

            <!-- Heading -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Shopping Cart
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $cart->items->count() }} item di keranjang anda
                </p>
            </div>

            @if ($cart->items->count() > 0)
                <div class="lg:flex lg:items-start lg:gap-8">

                    <!-- Cart Items -->
                    <div class="w-full space-y-4 lg:w-2/3">

                        @foreach ($cart->items as $item)
                            <div
                                class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">

                                <div class="flex flex-col gap-4 md:flex-row md:items-center">

                                    <!-- Product Image -->
                                    <div class="shrink-0">
                                        <img class="h-24 w-24 rounded-xl object-cover"
                                            src="{{ $item->product?->thumbnail }}" alt="{{ $item->product_name }}">
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1">

                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $item->product_name }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Rp{{ number_format($item->product_price, 0, ',', '.') }}
                                        </p>

                                        <!-- Action -->
                                        <div class="mt-4 flex flex-wrap items-center gap-4">

                                            <!-- Update Quantity -->
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">

                                                @csrf
                                                @method('PUT')

                                                <div
                                                    class="flex items-center overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">

                                                    <!-- Minus -->
                                                    <button type="submit" name="quantity"
                                                        value="{{ max(1, $item->quantity - 1) }}"
                                                        class="flex h-10 w-10 items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">

                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M20 12H4" />

                                                        </svg>
                                                    </button>

                                                    <!-- Quantity -->
                                                    <input type="text" value="{{ $item->quantity }}" readonly
                                                        class="h-10 w-14 border-0 bg-white text-center text-sm font-semibold text-gray-900 focus:ring-0 dark:bg-gray-800 dark:text-white">

                                                    <!-- Plus -->
                                                    <button type="submit" name="quantity"
                                                        value="{{ $item->quantity + 1 }}"
                                                        class="flex h-10 w-10 items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">

                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />

                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>

                                            <!-- Remove -->
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 text-sm font-medium text-red-600 hover:underline">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />

                                                    </svg>

                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="text-end">

                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Subtotal
                                        </p>

                                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 w-full lg:mt-0 lg:w-1/3">

                        <!-- Voucher Section -->
                        <x-voucher-section 
                            :appliedVouchers="$cart->getAppliedVouchers()"
                            :cartSummary="[
                                'total_discount' => $cart->total_discount_amount ?? 0,
                                'total_shipping_discount' => $cart->total_shipping_discount ?? 0,
                            ]"
                        />

                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-6">

                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                Order Summary
                            </h3>

                            <div class="mt-6 space-y-4">

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Total Item
                                    </span>

                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $cart->items->sum('quantity') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Subtotal
                                    </span>

                                    <span class="font-medium text-gray-900 dark:text-white">
                                        Rp{{ number_format($cart->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if ($cart->total_discount_amount > 0)
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            Diskon
                                        </span>

                                        <span class="font-medium text-green-600 dark:text-green-400">
                                            -Rp{{ number_format($cart->total_discount_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 pt-4 dark:border-gray-700">

                                    <div class="flex items-center justify-between">

                                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                                            Total
                                        </span>

                                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                                            Rp{{ number_format($cart->final_subtotal ?? $cart->subtotal - ($cart->total_discount_amount ?? 0), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Checkout -->
                                <a href="{{ route('checkout.index') }}"
                                    class="mt-6 flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700">

                                    Proceed to Checkout
                                </a>

                                <!-- Continue Shopping -->
                                <a href="{{ route('products') }}"
                                    class="flex w-full items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">

                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div
                    class="flex flex-col items-center justify-center rounded-2xl bg-white py-20 text-center shadow-sm dark:bg-gray-800">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2 5h12m-8 0a1 1 0 102 0m-2 0a1 1 0 112 0" />
                    </svg>

                    <h3 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">
                        Cart masih kosong
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Yuk tambahin produk dulu ke cart lu.
                    </p>

                    <a href="{{ route('products') }}"
                        class="mt-6 rounded-xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-700">

                        Belanja Sekarang
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-layout>
