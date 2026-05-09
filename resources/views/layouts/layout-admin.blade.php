<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <title>{{ config('app.name', 'Admin') }}</title>
</head>

<body class="h-full bg-gray-50">

    <div class="flex min-h-full">
        @include('layouts.navbar-admin')

        <main class="lg:ml-60 min-h-screen flex-1 overflow-auto pt-14 lg:pt-0 bg-gray-50">
            <div class="px-6 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    {{-- ==================== NOTIFICATION STACK ==================== --}}
    <div x-data="notificationManager()" @notify.window="add($event.detail)"
        class="fixed right-4 top-4 z-[9999] flex w-80 flex-col gap-2">

        <template x-for="notif in notifications" :key="notif.id">

            <div x-show="notif.visible" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                class="flex items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-sm"
                :class="{
                    'border-green-200': notif.type === 'success',
                    'border-red-200': notif.type === 'error',
                    'border-yellow-200': notif.type === 'warning',
                    'border-blue-200': notif.type === 'info',
                }">

                {{-- Icon --}}
                <div class="mt-0.5 shrink-0">

                    {{-- Success --}}
                    <template x-if="notif.type === 'success'">

                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />

                            </svg>

                        </div>

                    </template>

                    {{-- Error --}}
                    <template x-if="notif.type === 'error'">

                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />

                            </svg>

                        </div>

                    </template>

                    {{-- Warning --}}
                    <template x-if="notif.type === 'warning'">

                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-yellow-100">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />

                            </svg>

                        </div>

                    </template>

                    {{-- Info --}}
                    <template x-if="notif.type === 'info'">

                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>

                    </template>

                </div>



                {{-- Content --}}
                <div class="min-w-0 flex-1">

                    <p class="text-xs font-medium capitalize text-gray-900" x-text="notif.type">
                    </p>

                    <p class="mt-0.5 text-xs leading-relaxed text-gray-500" x-text="notif.message">
                    </p>

                    {{-- Progress --}}
                    <div class="mt-2 h-0.5 overflow-hidden rounded-full bg-gray-100">

                        <div class="h-full rounded-full transition-all ease-linear"
                            :class="{
                                'bg-green-400': notif.type === 'success',
                                'bg-red-400': notif.type === 'error',
                                'bg-yellow-400': notif.type === 'warning',
                                'bg-blue-400': notif.type === 'info',
                            }"
                            :style="`width: ${notif.progress}%; transition-duration: ${notif.duration}ms`">
                        </div>

                    </div>

                </div>



                {{-- Close --}}
                <button @click="dismiss(notif.id)" class="shrink-0 text-gray-300 transition-colors hover:text-gray-500">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

        </template>

    </div>



    {{-- ==================== SESSION NOTIFICATION ==================== --}}

    {{-- Success --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                setTimeout(() => {

                    window.dispatchEvent(
                        new CustomEvent('notify', {
                            detail: {
                                type: 'success',
                                message: @json(session('success'))
                            }
                        })
                    );

                }, 100);

            });
        </script>
    @endif



    {{-- Error --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                setTimeout(() => {

                    window.dispatchEvent(
                        new CustomEvent('notify', {
                            detail: {
                                type: 'error',
                                message: @json(session('error'))
                            }
                        })
                    );

                }, 100);

            });
        </script>
    @endif



    {{-- Validation Error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                setTimeout(() => {

                    @foreach ($errors->all() as $error)

                        window.dispatchEvent(
                            new CustomEvent('notify', {
                                detail: {
                                    type: 'error',
                                    message: @json($error)
                                }
                            })
                        );
                    @endforeach

                }, 100);

            });
        </script>
    @endif



    {{-- ==================== NOTIFICATION SCRIPT ==================== --}}
    <script>
        function notificationManager() {

            return {

                notifications: [],

                add({
                    type = 'info',
                    message,
                    duration = 4000
                }) {

                    const id = crypto.randomUUID();

                    this.notifications.push({

                        id,
                        type,
                        message,
                        duration,

                        progress: 100,
                        visible: true,

                    });

                    // Jalankan progress bar
                    this.$nextTick(() => {

                        const notif =
                            this.notifications.find(
                                n => n.id === id
                            );

                        if (notif) {
                            notif.progress = 0;
                        }

                    });

                    // Auto dismiss
                    setTimeout(() => {

                        this.dismiss(id);

                    }, duration);

                },

                dismiss(id) {

                    const notif =
                        this.notifications.find(
                            n => n.id === id
                        );

                    if (notif) {

                        notif.visible = false;

                        setTimeout(() => {

                            this.notifications =
                                this.notifications.filter(
                                    n => n.id !== id
                                );

                        }, 300);

                    }

                },

            }

        }
    </script>


</body>

</html>
