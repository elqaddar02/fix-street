<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Madinova</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
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
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-red-900 text-white sticky top-0 h-screen shadow-lg flex flex-col">
            <div class="px-6 py-5 border-b border-red-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="h-8 w-8 text-white" />
                    <span class="font-bold text-lg">Madinova Admin</span>
                </a>
            </div>

            <nav class="mt-4 px-2 space-y-1 flex-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Users</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Reports</a>
                <a href="{{ route('admin.cities.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.cities.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Cities</a>
                <a href="{{ route('admin.quartiers.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.quartiers.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Quartiers</a>
            </nav>

            <!-- Bottom Actions -->
            <div class="mt-auto px-2 py-4 border-t border-red-800">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-lg bg-red-700 text-white hover:bg-red-600 transition-colors font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
