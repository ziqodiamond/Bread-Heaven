<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            Konfirmasi Password
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            Demi keamanan akun Anda, silakan masukkan password terlebih dahulu
            sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">

        @csrf

        {{-- Password --}}
        <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Password
            </label>

            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="Masukkan password Anda"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Tombol Konfirmasi --}}
        <button type="submit"
            class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white py-3 font-medium transition duration-200">
            Konfirmasi
        </button>

    </form>

</x-guest-layout>
