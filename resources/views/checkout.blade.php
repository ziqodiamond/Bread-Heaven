<x-layout>
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <form action="{{ route('checkout.proceed') }}" method="POST" class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            @csrf
            <ol
                class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base">
                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Cart
                    </span>
                </li>
                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Checkout
                    </span>
                </li>
                <li class="flex shrink-0 items-center">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Order summary
                </li>
            </ol>

            <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12 xl:gap-16">
                <div class="min-w-0 flex-1 space-y-8">
                    <!-- Display Products Purchased -->
                    <div class="space-y-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Products in Your Order</h2>
                        @foreach ($cartItems as $item)
                            <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                        alt="{{ $item->product->name }}" class="w-16 h-16 rounded object-cover" />
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Quantity:
                                            {{ $item->quantity }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Price:
                                            Rp{{ number_format($item->product->price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @include('checkout.delivery')
                    @include('checkout.payment')
                    @include('checkout.delivery-method')
                </div>

                <div class="mt-6 w-full space-y-6 sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-md">
                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">

                            <!-- Subtotal -->
                            <div class="flex justify-between">
                                <span class="text-gray-500">Subtotal:</span>
                                <span id="subtotal" class="text-gray-900">Rp{{ number_format($totalPrice, 2) }}</span>
                            </div>

                            <!-- Shipping Cost -->
                            <div class="flex justify-between">
                                <span class="text-gray-500">Shipping Cost:</span>
                                <span id="shipping-cost" class="text-gray-900">Rp0.00</span>
                            </div>

                            <!-- Fee -->
                            <div class="flex justify-between">
                                <span class="text-gray-500">Fee:</span>
                                <span id="payment-fee" class="text-gray-900">Rp0.00</span>
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between font-bold">
                                <span class="text-gray-900">Total:</span>
                                <span id="total-price"
                                    class="text-gray-900">Rp{{ number_format($totalPrice, 2) }}</span>
                            </div>

                            <!-- Proceed to Payment Button -->
                            <button type="submit"
                                class="mt-4 w-full rounded-lg bg-primary-700 px-5 py-2.5 text-gray-900">Proceed to
                                Payment</button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proceed
                            to Payment</button>
                        <!-- Hidden input for total price -->
                        <input type="hidden" name="total_price" id="totalprice" value="{{ $totalPrice }}">
                    </div>
                </div>
            </div>
        </form>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subtotalElement = document.getElementById('subtotal');
            const shippingCostElement = document.getElementById('shipping-cost');
            const paymentFeeElement = document.getElementById('payment-fee');
            const totalPriceElement = document.getElementById('total-price');
            const totalpriceInput = document.getElementById('totalprice'); // Hidden input for total price

            let subtotal = parseFloat({{ $totalPrice }}); // Ambil subtotal dari server-side
            let shippingCost = 0;
            let paymentFee = 0;

            // Function to update total price
            function updateTotalPrice() {
                const total = subtotal + shippingCost + paymentFee;
                totalPriceElement.textContent = `Rp${total.toFixed(2)}`;

                // Update hidden input with total price
                totalpriceInput.value = total.toFixed(2);
            }

            // Function to handle shipping cost update based on selected delivery method
            function updateShippingCost() {
                const selectedRadio = document.querySelector(
                    'input[name="delivery-method"]:checked'); // Ambil radio button yang terpilih
                if (selectedRadio) {
                    const selectedShippingCost = parseFloat(selectedRadio.getAttribute('data-shipping-cost'));
                    shippingCost = isNaN(selectedShippingCost) ? 0 : selectedShippingCost;
                    shippingCostElement.textContent = `Rp${shippingCost.toFixed(2)}`;
                    updateTotalPrice(); // Update total harga setelah biaya pengiriman diperbarui
                }
            }

            // Function to handle payment fee update based on selected payment method
            function updatePaymentFee() {
                const selectedRadio = document.querySelector(
                    'input[name="payment-method"]:checked'); // Ambil radio button yang terpilih
                if (selectedRadio) {
                    const selectedPaymentFee = parseFloat(selectedRadio.getAttribute('data-fee'));
                    paymentFee = isNaN(selectedPaymentFee) ? 0 : selectedPaymentFee;
                    paymentFeeElement.textContent = `Rp${paymentFee.toFixed(2)}`;
                    updateTotalPrice(); // Update total harga setelah biaya pembayaran diperbarui
                }
            }

            // Event listener for delivery method change
            document.querySelectorAll('input[name="delivery-method"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    updateShippingCost
                        (); // Panggil fungsi untuk memperbarui biaya pengiriman saat terjadi perubahan
                });
            });

            // Event listener for payment method change
            document.querySelectorAll('input[name="payment-method"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    updatePaymentFee
                        (); // Panggil fungsi untuk memperbarui biaya pembayaran saat terjadi perubahan
                });
            });

            // Initial calculation (when page first loads)
            updateShippingCost
                (); // Jalankan fungsi ini langsung saat halaman pertama kali dimuat untuk opsi yang sudah terpilih
            updatePaymentFee
                (); // Jalankan fungsi ini langsung saat halaman pertama kali dimuat untuk opsi pembayaran yang sudah terpilih

            // Initial calculation for payment fee (if any default is selected)
            updateTotalPrice(); // Pastikan total harga juga langsung diperbarui
        });
    </script>


</x-layout>
