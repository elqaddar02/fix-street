<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'Madinup') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700&display=swap" rel="stylesheet" />

        {{-- The site's own build, like every other page. These pages used to load
             the Tailwind CDN, which lacks the forms plugin, so inputs had no borders. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col lg:flex-row">
            {{-- Desktop: brand panel. Phones only keep a slim logo bar, so the form starts near the top. --}}
            <aside class="relative hidden overflow-hidden bg-gradient-to-br from-red-600 to-red-800 text-white lg:flex lg:w-[44%] lg:flex-col lg:justify-between lg:px-14 lg:py-12">
                <div class="pointer-events-none absolute -end-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>

                <a href="{{ route('home') }}" class="relative flex w-fit items-center gap-2.5 rounded-lg">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white shadow-sm">
                        <x-application-logo class="h-8 w-auto" />
                    </span>
                    <span class="text-xl font-semibold tracking-tight">{{ config('app.name', 'Madinup') }}</span>
                </a>

                <div class="relative max-w-md">
                    <p class="text-xs font-semibold uppercase tracking-wider text-red-100">{{ __('Improving Your City') }}</p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight xl:text-4xl">
                        {{ __('Report Streets,') }}
                        <span class="block text-yellow-300">{{ __('Get Them Fixed') }}</span>
                    </h1>
                    <ul class="mt-8 space-y-4">
                        @foreach([
                            ['M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z', __('signin.benefit.report')],
                            ['M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z', __('signin.benefit.confirm')],
                            ['M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', __('signin.benefit.follow')],
                        ] as [$icon, $text])
                            <li class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                </span>
                                <span class="pt-1.5 text-sm leading-6 text-red-50">{{ $text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="relative text-xs text-red-100">&copy; {{ date('Y') }} {{ config('app.name', 'Madinup') }}. {{ __('footer.independent') }}</p>
            </aside>

            <main class="flex flex-1 flex-col">
                <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg">
                        <x-application-logo class="h-9 w-auto" />
                        <span class="text-lg font-semibold tracking-tight text-gray-900">{{ config('app.name', 'Madinup') }}</span>
                    </a>
                    <x-language-switcher />
                </div>
                <div class="hidden justify-end px-8 pt-6 lg:flex">
                    <x-language-switcher />
                </div>

                <div class="flex flex-1 items-start justify-center px-4 pb-10 pt-2 sm:px-6 sm:pt-6 lg:items-center lg:pt-0">
                    <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white px-5 py-7 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:px-8 sm:py-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
