<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Fix Street') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="text-lg font-bold text-indigo-600">Fix Street</a>

            <nav class="flex items-center gap-2 sm:gap-4">
                <a href="{{ url('/') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Home</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Dashboard</a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.reports.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Admin Reports</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Login</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="mx-auto max-w-7xl px-4 pb-16 pt-16 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 p-8 text-white shadow-lg sm:p-12">
                <p class="mb-3 inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide">Community Action</p>
                <h1 class="text-3xl font-bold sm:text-5xl">Help improve our city</h1>
                <p class="mt-4 max-w-2xl text-sm text-indigo-50 sm:text-base">
                    Report damaged roads, broken lights, and other public issues so they can be handled faster by the right teams.
                </p>
                <div class="mt-8">
                    @auth
                        <a href="{{ route('reports.create') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-3 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50">
                            Report a Problem
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-3 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50">
                            Report a Problem
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900">Latest Reports</h2>
                @auth
                    <a href="{{ route('reports.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View all</a>
                @endauth
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($latestReports as $report)
                    <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="line-clamp-1 text-base font-semibold text-slate-900">{{ $report->title }}</h3>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="rounded bg-slate-100 px-2 py-1">{{ $report->category->name }}</span>
                            <span class="rounded bg-slate-100 px-2 py-1">{{ $report->city->name }}</span>
                            @php
                                $statusClass = match ($report->status) {
                                    'OPEN' => 'bg-red-100 text-red-700',
                                    'IN_PROGRESS' => 'bg-amber-100 text-amber-700',
                                    'RESOLVED' => 'bg-emerald-100 text-emerald-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="rounded px-2 py-1 font-semibold {{ $statusClass }}">{{ $report->status }}</span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 md:col-span-2 lg:col-span-3">
                        No reports yet. Be the first to submit one.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm sm:p-10">
                <h2 class="text-2xl font-bold text-slate-900">Why keeping the city clean matters</h2>
                <p class="mt-4 text-slate-600">
                    A clean and well-maintained city improves public health, safety, and comfort for everyone. Streets free from damage and waste reduce accidents,
                    support local businesses, and create a better environment for families.
                </p>
                <p class="mt-4 text-slate-600">
                    Your report helps authorities prioritize real issues faster. By participating, citizens become part of a shared effort to build a safer and more
                    livable city.
                </p>
            </div>
        </section>
    </main>
</body>
</html>
