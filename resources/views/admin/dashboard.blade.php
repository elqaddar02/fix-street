@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    // Explicit class strings per card. Tailwind scans source text, so
    // interpolated names like "bg-{$color}-50" are never generated —
    // that is why the old dashboard needed the CDN build to look right.
    $cards = [
        [
            'title' => 'Utilisateurs',
            'value' => $totalUsers,
            'trend' => $trends['users'],
            'caption' => 'inscrits',
            'ring' => 'bg-blue-50 text-blue-600',
            'icon' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z',
        ],
        [
            'title' => 'Signalements',
            'value' => $totalReports,
            'trend' => $trends['reports'],
            'caption' => 'au total',
            'ring' => 'bg-amber-50 text-amber-600',
            'icon' => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        ],
        [
            'title' => 'Commentaires',
            'value' => $totalComments,
            'trend' => $trends['comments'],
            'caption' => $pendingComments . ' en attente',
            'ring' => 'bg-emerald-50 text-emerald-600',
            'icon' => 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z',
        ],
        [
            'title' => 'Taux de résolution',
            'value' => $resolutionRate,
            'suffix' => '%',
            'trend' => null,
            'caption' => $resolvedReports . ' résolus sur ' . $totalReports,
            'ring' => 'bg-indigo-50 text-indigo-600',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $statusMeta = [
        'OPEN' => ['label' => 'Ouverts', 'dot' => 'bg-amber-500'],
        'IN_PROGRESS' => ['label' => 'En cours', 'dot' => 'bg-sky-500'],
        'RESOLVED' => ['label' => 'Résolus', 'dot' => 'bg-emerald-500'],
        'REJECTED' => ['label' => 'Rejetés', 'dot' => 'bg-rose-500'],
    ];
@endphp

<div class="space-y-6">

    <div>
        <h2 class="text-2xl font-bold tracking-tight">Vue d'ensemble</h2>
        <p class="mt-1 text-sm text-slate-500">Activité de la plateforme sur les 30 derniers jours.</p>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 transition-shadow hover:shadow-sm">
                <div class="flex items-start justify-between">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $card['ring'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                        </svg>
                    </span>

                    @if($card['trend'])
                        @if($card['trend']['percent'] === null)
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-500">nouveau</span>
                        @else
                            <span @class([
                                'inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[11px] font-semibold',
                                'bg-emerald-50 text-emerald-700' => $card['trend']['up'],
                                'bg-rose-50 text-rose-700' => ! $card['trend']['up'],
                            ])>
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['trend']['up'] ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}"/>
                                </svg>
                                {{ $card['trend']['percent'] > 0 ? '+' : '' }}{{ $card['trend']['percent'] }}%
                            </span>
                        @endif
                    @endif
                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $card['title'] }}</p>
                <p class="mt-1 text-3xl font-bold tracking-tight tabular-nums">
                    {{ number_format($card['value'], 0, ',', ' ') }}{{ $card['suffix'] ?? '' }}
                </p>
                <p class="mt-1 text-xs text-slate-500">{{ $card['caption'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Activity + status --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
            <div class="mb-2 flex items-baseline justify-between">
                <div>
                    <h3 class="text-sm font-semibold">Signalements par jour</h3>
                    <p class="text-xs text-slate-500">30 derniers jours</p>
                </div>
                <span class="text-sm font-semibold tabular-nums text-slate-600">
                    {{ $trends['reports']['current'] }} sur la période
                </span>
            </div>
            <div id="chart-activity" class="-mx-2"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-sm font-semibold">Répartition par statut</h3>
            <p class="text-xs text-slate-500">Tous signalements confondus</p>

            <div id="chart-status" class="my-2"></div>

            <div class="mt-4 space-y-1">
                @foreach($statusMeta as $key => $meta)
                    <a href="{{ route('admin.reports.index', ['status' => $key]) }}"
                       class="flex items-center justify-between rounded-lg px-2 py-2 transition-colors hover:bg-slate-50">
                        <span class="flex items-center gap-2.5 text-sm text-slate-600">
                            <span class="h-2 w-2 rounded-full {{ $meta['dot'] }}"></span>
                            {{ $meta['label'] }}
                        </span>
                        <span class="text-sm font-semibold tabular-nums">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Categories + recent --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-sm font-semibold">Catégories les plus signalées</h3>
            <p class="mb-2 text-xs text-slate-500">Volume par catégorie</p>
            @if(count($reportsByCategory))
                <div id="chart-categories" class="-mx-2"></div>
            @else
                <p class="py-12 text-center text-sm text-slate-400">Aucun signalement pour le moment.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold">Derniers signalements</h3>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Tout voir</a>
            </div>

            <ul class="divide-y divide-slate-100">
                @forelse($latestReports as $report)
                    <li>
                        <a href="{{ route('admin.reports.show', $report) }}" class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-3 transition-colors hover:bg-slate-50">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $report->title }}</p>
                                <p class="truncate text-xs text-slate-500">
                                    {{ $report->city->name ?? 'Ville inconnue' }} · {{ $report->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <x-admin.status-pill :status="$report->status" />
                        </a>
                    </li>
                @empty
                    <li class="py-12 text-center text-sm text-slate-400">Aucun signalement.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Newest members --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-semibold">Nouveaux membres</h3>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Tout voir</a>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($latestUsers as $user)
                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition-colors hover:border-slate-200 hover:bg-slate-50">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-slate-400 sm:col-span-2 xl:col-span-4">Aucun membre.</p>
            @endforelse
        </div>
    </div>
</div>

@php
    // Built here rather than inline: Blade's @json directive cannot parse
    // nested array literals in its argument.
    $chartPayload = [
        'activity' => $reportsPerDay,
        'status' => [
            'labels' => array_values(array_map(fn ($meta) => $meta['label'], $statusMeta)),
            'values' => array_values($statusCounts),
        ],
        'categories' => [
            'labels' => array_keys($reportsByCategory),
            'values' => array_values($reportsByCategory),
        ],
    ];
@endphp

<script type="application/json" id="dashboard-data">
    @json($chartPayload)
</script>
<script>
    window.dashboardData = JSON.parse(document.getElementById('dashboard-data').textContent);
</script>
@endsection
