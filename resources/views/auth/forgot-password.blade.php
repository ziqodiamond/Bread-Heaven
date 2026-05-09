<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Lupa Password
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            Tidak masalah. Masukkan email akun Anda dan kami akan mengirimkan
            link untuk reset password.
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">

        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Email
            </label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="Masukkan email Anda"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Tombol Kirim --}}
        <button type="submit"
            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
            Kirim Link Reset Password
        </button>

    </form>

</x-guest-layout>
