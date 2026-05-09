<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Reset Password
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Masukkan password baru untuk akun Anda
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">

        @csrf

        {{-- Token Reset Password --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Email
            </label>

            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                autofocus autocomplete="username" placeholder="Masukkan email"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password Baru --}}
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Password Baru
            </label>

            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Masukkan password baru"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Konfirmasi Password
            </label>

            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="Ulangi password baru"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Tombol Reset --}}
        <button type="submit"
            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
            Reset Password
        </button>

    </form>

</x-guest-layout>
