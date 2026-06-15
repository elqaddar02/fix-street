<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Report street problems and help improve your city with Madinova.">

        <title>{{ $title ?? config('app.name', 'Madinova') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif

        <!-- Leaflet CSS and JS for maps -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex flex-col min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow pt-24">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content - Flex Grow -->
            <main class="flex-grow">
                {{ $slot }}
            </main>

            <!-- Terms Modal -->
            <div id="termsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-label="Terms of Service">
                <div class="w-full max-w-3xl rounded-xl bg-white p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <h2 class="text-xl font-semibold">Terms of Service</h2>
                        <button onclick="closeTermsModal()" class="text-gray-700 hover:text-gray-900">✕</button>
                    </div>
                    <div class="h-72 overflow-y-auto text-sm leading-relaxed text-gray-700">
                        @include('termscontent')
                    </div>
                    <div class="mt-6 text-right">
                        <button onclick="closeTermsModal()" class="rounded bg-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-300">Close</button>
                    </div>
                </div>
            </div>

            <!-- Modern Footer -->
            @include('layouts.footer')
        </div>

        <script>
            function openTermsModal() {
                const modal = document.getElementById('termsModal');
                if (modal) modal.classList.remove('hidden');
            }

            function closeTermsModal() {
                const modal = document.getElementById('termsModal');
                if (modal) modal.classList.add('hidden');
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeTermsModal();
            });
        </script>
    </body>
</html>
