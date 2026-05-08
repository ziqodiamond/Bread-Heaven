<x-layout>
    <div class="container mx-auto mt-5">
        <h2 class="text-xl font-bold mb-4">Transaction Details</h2>

        <!-- Status Transaksi -->
        @if ($transaction->status === 'pending')
            <div class="bg-yellow-400 text-white rounded-lg p-4 mb-4 shadow-lg">
                <div class="text-l text-center font-semibold">status :
                    <span class="font-bold">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        @elseif($transaction->status === 'process')
            <div class="bg-blue-500 text-white rounded-lg p-4 mb-4 shadow-lg">
                <div class="text-l text-center font-semibold">status :
                    <span class="font-bold">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        @elseif($transaction->status === 'shipped')
            <div class="bg-indigo-500  text-white rounded-lg p-4 mb-4 shadow-lg">
                <div class="text-l text-center font-semibold">status :
                    <span class="font-bold">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        @elseif($transaction->status === 'completed')
            bg-green-500
            <div class="bg-green-500  text-white rounded-lg p-4 mb-4 shadow-lg">
                <div class="text-l text-center font-semibold">status :
                    <span class="font-bold">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        @else
            bg-red-500
            <div class=" bg-red-500 text-white rounded-lg p-4 mb-4 shadow-lg">
                <div class="text-l text-center font-semibold">status :
                    <span class="font-bold">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        @endif


        <!-- Informasi Detail Transaksi -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-sm">
            <h3 class="text-lg font-semibold mb-4 dark:text-white">Order Information</h3>

            <p class="text-gray-700 dark:text-gray-200"><strong>Customer Name:</strong>
                {{ $transaction->customer_name }}
            </p>
            <p class="text-gray-700 dark:text-gray-200"><strong>Phone:</strong> {{ $transaction->customer_phone }}</p>
            <p class="text-gray-700 dark:text-gray-200"><strong>Email:</strong> {{ $transaction->customer_email }}</p>
            <p class="text-gray-700 dark:text-gray-200"><strong>Address:</strong> {{ $transaction->customer_address }}
            </p>
            <p class="text-gray-700 dark:text-gray-200"><strong>Delivery Method:</strong>
                {{ $transaction->deliveryMethod->name }} (Cost: Rp
                {{ number_format($transaction->deliveryMethod->shipping_cost, 0, ',', '.') }})</p>
            <p class="text-gray-700 dark:text-gray-200"><strong>Payment Method:</strong>
                {{ $transaction->paymentMethod->name }} (Cost: Rp
                {{ number_format($transaction->paymentMethod->fee, 0, ',', '.') }})</p>


            <h3 class="text-lg font-semibold mt-4 mb-2 dark:text-white">Items Transacition</h3>
            <table class="w-full mb-4">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Product</th>
                        <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Quantity</th>
                        <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Price</th>
                        <th class="border px-4 py-2 text-gray-700 dark:text-gray-200">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->details as $detail)
                        <tr>
                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ $detail->product->name }}
                            </td>
                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">{{ $detail->quantity }}</td>
                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">Rp
                                {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-200">Rp
                                {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($transaction->payment_status === 'unpaid')
                <div class="bg-yellow-400 text-white rounded-lg p-4 mb-4 shadow-lg">
                    <div class="text-l text-center font-semibold">status pembayaran :
                        <span class="font-bold">{{ ucfirst($transaction->payment_status) }}</span>
                    </div>
                </div>
            @elseif($transaction->payment_status === 'paid')
                <div class="bg-green-500 text-white rounded-lg p-4 mb-4 shadow-lg">
                    <div class="text-l text-center font-semibold">status pembayaran :
                        <span class="font-bold">{{ ucfirst($transaction->payment_status) }}</span>
                    </div>
                </div>
            @else
                <div class="bg-red-500 text-white rounded-lg p-4 mb-4 shadow-lg">
                    <div class="text-l text-center font-semibold">status :
                        <span class="font-bold">{{ ucfirst($transaction->payment_status) }}</span>
                    </div>
                </div>
            @endif
            <!-- Total Amount dan Copy -->
            <div class="text-center mb-6">
                <p class="text-m  text-blue-800 dark:text-blue-300">total</p>
                <p class="text-2xl font-bold text-blue-800 dark:text-blue-300" id="totalPrice">
                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                    <button onclick="copyText('totalPrice')" class="inline-block ml-2">
                        <svg class="w-[22px] h-[22px] text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                d="M9 8v3a1 1 0 0 1-1 1H5m11 4h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v1m4 3v10a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7.13a1 1 0 0 1 .24-.65L7.7 8.35A1 1 0 0 1 8.46 8H13a1 1 0 0 1 1 1Z" />
                        </svg>
                    </button>
                </p>
            </div>

            <!-- Payment Method dan Account Number -->
            <div class="text-center">
                @if ($paymentMethod)
                    @if ($paymentMethod->image_url)
                        <p class="text-m  text-blue-800 dark:text-blue-300">QR code</p>
                        <img src="{{ asset('storage/' . $paymentMethod->image_url) }}" alt="Payment Method Image"
                            class="w-52 mb-4 mx-auto">
                    @else
                        <p class="text-m text-blue-800 dark:text-blue-300">no rekening</p>
                        <p class="text-2xl font-bold text-blue-800 dark:text-blue-300" id="accountNumber">
                            {{ $paymentMethod->account_number }}
                            <button onclick="copyText('accountNumber')" class="inline-block ml-2">
                                <svg class="w-[22px] h-[22px] text-gray-800 dark:text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                        d="M9 8v3a1 1 0 0 1-1 1H5m11 4h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v1m4 3v10a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7.13a1 1 0 0 1 .24-.65L7.7 8.35A1 1 0 0 1 8.46 8H13a1 1 0 0 1 1 1Z" />
                                </svg>
                            </button>
                        </p>
                    @endif
                @endif
            </div>

            <!-- Instruksi Pembayaran -->
            @if ($transaction->payment_status === 'unpaid')
                <div class="mt-6 text-center">
                    <p class="text-red-500 font-bold">Please make your payment as soon as possible!</p>
                    <p class="text-gray-600 dark:text-gray-300">The payment will be manually checked, please be patient
                        for the confirmation.</p>
                </div>
            @endif
        </div>

        <!-- Button to Cancel Transaction if it's pending and unpaid -->
        @if ($transaction->status === 'pending' && $transaction->payment_status === 'unpaid')
            <div class="mt-4 mb-4 text-center">
                <a href="{{ route('transaction.cancel', $transaction->id) }}"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Cancel Transaction
                </a>
            </div>
        @endif
    </div>

    <script>
        function copyText(elementId) {
            const element = document.getElementById(elementId);
            const textToCopy = element.textContent || element.innerText;
            navigator.clipboard.writeText(textToCopy).then(() => {
                alert("Copied: " + textToCopy);
            });
        }
    </script>
</x-layout>
