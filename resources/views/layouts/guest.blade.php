<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;900&display=swap" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: {
                                sans: ['Figtree', 'sans-serif'],
                            },
                        },
                    },
                }
            </script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif

        <style>
            .auth-hero-gradient { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Brand panel -->
            <div class="auth-hero-gradient relative overflow-hidden text-white lg:w-1/2 flex flex-col justify-between px-8 py-10 lg:px-16 lg:py-16">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                </div>

                <a href="/" class="relative z-10 flex items-center gap-3">
                    <x-application-logo class="h-9 w-9" />
                    <span class="font-black text-xl tracking-tight">Madinova</span>
                </a>

                <div class="relative z-10 hidden lg:block max-w-md">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="w-2 h-2 bg-yellow-300 rounded-full"></div>
                        <span class="text-yellow-100 text-sm font-bold uppercase tracking-widest">{{ __('Improving Your City') }}</span>
                    </div>
                    <h1 class="text-4xl font-black leading-tight mb-4">
                        {{ __('Report Streets,') }}<br>
                        <span class="text-yellow-300">{{ __('Get Them Fixed') }}</span>
                    </h1>
                    <p class="text-gray-100 leading-relaxed">
                        {{ __('Help us maintain safe, clean streets. Report potholes, broken lights, damaged infrastructure, and more.') }}
                    </p>
                </div>

                <p class="relative z-10 text-sm text-red-100 hidden lg:block">
                    &copy; {{ date('Y') }} Madinova
                </p>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex items-center justify-center px-6 py-12 sm:px-10 bg-gray-50">
                <div class="w-full max-w-md">
                    <div class="bg-white px-6 py-8 sm:px-10 sm:py-10 shadow-xl rounded-2xl border border-gray-100">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
