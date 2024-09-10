<div class="space-y-4">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Payment</h3>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach ($paymentMethods as $paymentMethod)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input id="payment-method-{{ $paymentMethod->id }}"
                            aria-describedby="payment-method-text-{{ $paymentMethod->id }}" type="radio"
                            name="payment-method" value="{{ $paymentMethod->id }}"
                            class="h-4 w-4 border-gray-300 bg-white text-primary-600 focus:ring-2 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-primary-600"
                            {{ $loop->first ? 'checked' : '' }} />
                    </div>

                    <div class="ms-4 text-sm">
                        <label for="payment-method-{{ $paymentMethod->id }}"
                            class="font-medium leading-none text-gray-900 dark:text-white">
                            {{ $paymentMethod->name }}
                        </label>
                        <p id="payment-method-text-{{ $paymentMethod->id }}"
                            class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                            {{ $paymentMethod->category }}
                            {{-- @if ($paymentMethod->fee > 0)
                                +${{ number_format($paymentMethod->fee, 2) }}
                            @endif --}}
                        </p>
                    </div>
                </div>


            </div>
        @endforeach
    </div>
</div>
