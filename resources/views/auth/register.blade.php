<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Buat Akun
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Daftar untuk mulai menggunakan aplikasi
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">

        @csrf

        {{-- Nama --}}
        <div>
            <label for="name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Nama Lengkap
            </label>

            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="Masukkan nama lengkap"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Email
            </label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="username" placeholder="Masukkan email"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <label for="phone" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Nomor Telepon
            </label>

            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                placeholder="08xxxxxxxxxx"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Password
            </label>

            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Masukkan password"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Konfirmasi Password
            </label>

            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="Ulangi password"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Footer --}}
        <div class="flex flex-col gap-4 pt-2">

            {{-- Tombol Register --}}
            <button type="submit"
                class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
                Daftar
            </button>

            {{-- Link Login --}}
            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                Sudah punya akun?

                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700">
                    Masuk sekarang
                </a>
            </p>

        </div>

    </form>

</x-guest-layout>
