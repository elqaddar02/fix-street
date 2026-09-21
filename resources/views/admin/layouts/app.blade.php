<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Madinup</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

@php
    $nav = [
        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Tableau de bord', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10'],
        ['route' => 'admin.reports.index', 'pattern' => 'admin.reports.*', 'label' => 'Signalements', 'icon' => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Utilisateurs', 'icon' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75'],
        ['route' => 'admin.comments.index', 'pattern' => 'admin.comments.*', 'label' => 'Commentaires', 'icon' => 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z'],
        ['route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'label' => 'Catégories', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
        ['route' => 'admin.cities.index', 'pattern' => 'admin.cities.*', 'label' => 'Villes', 'icon' => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z M12 10a2 2 0 100-4 2 2 0 000 4z'],
        ['route' => 'admin.ads.index', 'pattern' => 'admin.ads.*', 'label' => 'Publicités', 'icon' => 'M3 11l18-5v12L3 14v-3z M11.6 16.8a3 3 0 11-5.8-1.6'],
    ];
@endphp

<div x-data="{ open: false }" class="flex min-h-screen">

    {{-- Mobile backdrop --}}
    <div x-show="open"
         x-transition.opacity
         @click="open = false"
         class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden"
         style="display: none"></div>

    <aside :class="open ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">

        <div class="flex h-16 items-center gap-3 border-b border-slate-100 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-600 text-white">
                    <x-application-logo class="h-5 w-5 fill-current" />
                </span>
                <span class="text-[15px] font-bold tracking-tight">Madinup</span>
            </a>
            <button @click="open = false" class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
            @foreach($nav as $item)
                @php $current = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   @class([
                       'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                       'bg-red-50 text-red-700' => $current,
                       'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $current,
                   ])>
                    <svg @class(['h-[18px] w-[18px] shrink-0', 'text-red-600' => $current, 'text-slate-400 group-hover:text-slate-500' => ! $current])
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-100 p-3">
            <div class="mb-2 flex items-center gap-3 rounded-xl px-3 py-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-700">
                    <svg class="h-[18px] w-[18px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8">
            <button @click="open = true" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="truncate text-base font-semibold">@yield('title', 'Administration')</h1>
            <a href="{{ route('home') }}" class="ml-auto hidden items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 sm:inline-flex">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Voir le site
            </a>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
            @yield('content')
        </main>
    </div>
</div>

{{-- Flash messages are surfaced through the same toast stack as the inline actions. --}}
@if (session('success') || session('error'))
    @php
        $flash = [
            'message' => session('success') ?: session('error'),
            'tone' => session('success') ? 'success' : 'error',
        ];
    @endphp
    <script type="application/json" id="flash-message">
        @json($flash)
    </script>
@endif

@stack('scripts')
</body>
</html>
