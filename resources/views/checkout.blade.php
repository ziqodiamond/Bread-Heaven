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
                    @include('checkout.delivery')
                    @include('checkout.payment')
                    @include('checkout.delivery-method')
                </div>

                <div class="mt-6 w-full space-y-6 sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-md">
                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($totalPrice, 2) }}</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                                <dd class="text-base font-medium text-green-500">$0</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Store Pickup</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">$99</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">$199</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($totalPrice + 99 + 199, 2) }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proceed
                            to Payment</button>
                        <p class="text-sm font-normal text-gray-500 dark:text-gray-400">One or more items in your cart
                            require an account. <a href="#" title=""
                                class="font-medium text-primary-700 underline hover:no-underline dark:text-primary-500">Sign
                                in or create an account now.</a>.</p>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout>
