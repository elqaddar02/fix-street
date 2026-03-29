<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Madinova</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-red-900 text-white sticky top-0 h-screen shadow-lg">
            <div class="px-6 py-5 border-b border-red-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="h-8 w-8 text-white" />
                    <span class="font-bold text-lg">Madinova Admin</span>
                </a>
            </div>

            <nav class="mt-4 px-2 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Users</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Reports</a>
                <a href="{{ route('admin.cities.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.cities.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Cities</a>
            </nav>
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
