<x-layout>

    <div class="mx-auto max-w-3xl px-4 py-10">

        <div class="rounded-2xl border border-gray-200 bg-white p-8 dark:border-neutral-800 dark:bg-neutral-900">

            <div class="mb-8">

                <p class="text-sm text-gray-500">
                    Pembayaran Pesanan
                </p>

                <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $order->invoice_number }}
                </h1>

            </div>

            {{-- ===================================================== --}}
            {{-- STATUS --}}
            {{-- ===================================================== --}}

            <div class="mb-8 rounded-xl bg-yellow-50 p-5 dark:bg-yellow-900/20">

                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    Status Pembayaran:
                </p>

                <h2 class="mt-1 text-xl font-bold text-yellow-800 dark:text-yellow-200">
                    {{ strtoupper($transaction->transaction_status) }}
                </h2>

            </div>

            {{-- ===================================================== --}}
            {{-- TOTAL --}}
            {{-- ===================================================== --}}

            <div class="mb-8">

                <p class="text-sm text-gray-500">
                    Total Pembayaran
                </p>

                <h2 class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                </h2>

            </div>

            {{-- ===================================================== --}}
            {{-- BUTTON BAYAR --}}
            {{-- ===================================================== --}}

            @if ($transaction->payment_url)
                <a href="{{ $transaction->payment_url }}" target="_blank"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-black px-6 py-4 text-base font-semibold text-white transition hover:opacity-90 dark:bg-white dark:text-black">

                    Lanjutkan Pembayaran

                </a>
            @endif

        </div>

    </div>

</x-layout>
