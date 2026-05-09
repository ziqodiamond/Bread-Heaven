<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex bg-gray-100 dark:bg-gray-900">

        {{-- Bagian kiri / branding --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-blue-600">

            {{-- Jika nanti mau pakai gambar tinggal uncomment --}}
            {{--
            <img
                src="{{ asset('storage/images/auth-bg.jpg') }}"
                alt="Background"
                class="absolute inset-0 w-full h-full object-cover">
            --}}

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-blue-700/80"></div>

            {{-- Content branding --}}
            <div class="relative z-10 flex flex-col justify-between w-full p-12 text-white">
                <div>
                    <img src="{{ asset('storage/images/logo-app.png') }}" alt="Logo" class="h-16 w-auto">
                </div>

                <div class="max-w-md">
                    <h1 class="text-5xl font-bold leading-tight">
                        Selamat Datang
                    </h1>

                    <p class="mt-6 text-lg text-blue-100">
                        Kelola aplikasi lu dengan pengalaman yang modern,
                        cepat, dan nyaman digunakan.
                    </p>
                </div>

                <div class="text-sm text-blue-100">
                    © {{ date('Y') }} Hadziq
                </div>
            </div>
        </div>

        {{-- Bagian kanan / form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">

                {{-- Logo mobile --}}
                <div class="lg:hidden mb-8 text-center">
                    <img src="{{ asset('storage/images/logo-app.png') }}" alt="Logo" class="mx-auto h-16 w-auto">
                </div>

                {{-- Content auth --}}
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
