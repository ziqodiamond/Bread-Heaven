<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Masuk
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Silakan masuk ke akun Anda
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">

        @csrf

        {{-- Email / Username --}}
        <div>
            <label for="id_user" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Email atau Kode Pengguna
            </label>

            <input id="id_user" type="text" name="id_user" value="{{ old('id_user') }}" required autofocus
                autocomplete="username" placeholder="Masukkan email atau kode pengguna"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Password
            </label>

            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="Masukkan password"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
        </div>

        {{-- Remember Me + Forgot Password --}}
        <div class="flex items-center justify-between">

            {{-- Remember Me --}}
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800">

                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Ingat saya
                </span>
            </label>

            {{-- Forgot Password --}}
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                    Lupa password?
                </a>
            @endif

        </div>

        {{-- Tombol Login --}}
        <button type="submit"
            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
            Masuk
        </button>
        {{-- Register --}}
        @if (Route::has('register'))
            <div class="text-center pt-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Belum punya akun?

                    <a href="{{ route('register') }}"
                        class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        @endif

    </form>

</x-guest-layout>
