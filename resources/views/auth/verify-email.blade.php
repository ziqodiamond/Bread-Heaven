<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Verifikasi Email
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            Terima kasih sudah mendaftar. Sebelum mulai menggunakan aplikasi,
            silakan verifikasi alamat email Anda melalui link yang telah kami kirimkan.
        </p>
    </div>

    {{-- Status Success --}}
    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">

            Link verifikasi baru berhasil dikirim ke email Anda.

        </div>
    @endif

    {{-- Action --}}
    <div class="space-y-4">

        {{-- Resend Verification --}}
        <form method="POST" action="{{ route('verification.send') }}">

            @csrf

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
                Kirim Ulang Email Verifikasi
            </button>

        </form>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button type="submit"
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 py-3 font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                Keluar
            </button>

        </form>

    </div>

</x-guest-layout>
