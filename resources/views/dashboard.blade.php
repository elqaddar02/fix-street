@php
    $title = __('nav.my_reports') . ' - Madinup';

    $statusMeta = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'count' => $openReports, 'bar' => 'bg-amber-500'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'count' => $inProgressReports, 'bar' => 'bg-sky-600'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'count' => $resolvedReports, 'bar' => 'bg-emerald-600'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'count' => $rejectedReports, 'bar' => 'bg-gray-400'],
    ];

    $sortedReportsByCategory = collect($reportsByCategory)->sortDesc();

    $thisMonth = $reportsByMonth->last()['count'];
    $monthDiff = $thisMonth - $reportsByMonth->get($reportsByMonth->count() - 2)['count'];
    $resolvedShare = $totalReports > 0 ? round($resolvedReports / $totalReports * 100) : 0;

    // The current month is drawn in the brand colour, earlier months in a light tint.
    $maxMonth = max(1, $reportsByMonth->max('count'));
    $currentIndex = $reportsByMonth->count() - 1;
    $monthLabel = fn ($month) => $month->copy()->locale(app()->getLocale())->translatedFormat('M');

    // Donut slices for the category chart: the five largest, the rest folded into one.
    $categoryColors = ['#dc2626', '#f59e0b', '#0284c7', '#059669', '#7c3aed'];
    $categorySlices = $sortedReportsByCategory->take(5)->values()->map(fn ($count, $i) => [
        'label' => $sortedReportsByCategory->keys()[$i] ?: __('dashboard.labels.unknown'),
        'count' => $count,
        'color' => $categoryColors[$i],
    ]);
    if ($sortedReportsByCategory->count() > 5) {
        $categorySlices->push(['label' => __('dashboard.labels.other'), 'count' => $sortedReportsByCategory->slice(5)->sum(), 'color' => '#9ca3af']);
    }
    $categoryTotal = max(1, $categorySlices->sum('count'));

    $tile = 'flex min-w-0 flex-col gap-1 rounded-xl border bg-white p-4 sm:p-5';
    $tileShadow = 'border-gray-200 shadow-[0_3px_12px_rgba(17,24,39,0.04)]';
    $eyebrow = 'truncate text-[11px] font-medium uppercase tracking-wider text-gray-500';
    $figure = 'text-[32px] font-bold leading-none tabular-nums text-gray-900';
    $detail = 'text-[12.5px] text-gray-500';
    $module = 'flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:p-5';
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-4 border-b border-gray-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-gray-500">{{ __('nav.my_reports') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-[1.75rem]">
                    {{ __('dashboard.greeting', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1.5 text-sm text-gray-500">{{ __('dashboard.subtitle') }}</p>
            </div>

            <a href="{{ route('reports.create') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                {{ __('dashboard.new_report') }}
            </a>
        </header>

        <section aria-label="{{ __('dashboard.stats.total') }}" class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="{{ $tile }} border-red-200 shadow-[0_5px_18px_rgba(220,38,38,0.08)]">
                <span class="{{ $eyebrow }}">{{ __('dashboard.stats.total') }}</span>
                <span class="{{ $figure }}">{{ $totalReports }}</span>
                <span class="{{ $detail }}">
                    <b @class(['font-semibold', 'text-gray-900' => $monthDiff !== 0])>{{ $monthDiff > 0 ? '+' : '' }}{{ $monthDiff }}</b>
                    {{ __('dashboard.stats.vs_last_month') }}
                </span>
            </article>
            <article class="{{ $tile }} {{ $tileShadow }}">
                <span class="{{ $eyebrow }}">{{ $statusMeta['OPEN']['label'] }}</span>
                <span class="{{ $figure }}">{{ $openReports }}</span>
                <span class="{{ $detail }}">{{ __('dashboard.stats.awaiting') }}</span>
            </article>
            <article class="{{ $tile }} {{ $tileShadow }}">
                <span class="{{ $eyebrow }}">{{ $statusMeta['IN_PROGRESS']['label'] }}</span>
                <span class="{{ $figure }}">{{ $inProgressReports }}</span>
                <span class="{{ $detail }}">{{ __('dashboard.stats.being_handled') }}</span>
            </article>
            <article class="{{ $tile }} {{ $tileShadow }}">
                <span class="{{ $eyebrow }}">{{ $statusMeta['RESOLVED']['label'] }}</span>
                <span class="{{ $figure }}">{{ $resolvedReports }}</span>
                <span class="{{ $detail }}">
                    <b class="font-semibold text-emerald-700">{{ $resolvedShare }}%</b>
                    {{ __('dashboard.stats.of_your_reports') }}
                </span>
            </article>
        </section>

        @if($totalReports === 0)
            <section class="mt-6 rounded-xl border border-gray-200 bg-white px-6 py-12 text-center">
                <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.empty.headline') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ __('dashboard.empty.body') }}</p>
                <a href="{{ route('reports.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                    {{ __('dashboard.new_report') }}
                </a>
            </section>
        @else
            <div class="mt-3 grid gap-3 lg:grid-cols-3">
                <section class="{{ $module }}" aria-labelledby="status-breakdown-title">
                    <header class="flex items-baseline justify-between gap-3">
                        <h2 id="status-breakdown-title" class="text-base font-semibold leading-snug text-gray-900">{{ __('dashboard.breakdown.title') }}</h2>
                        <span class="shrink-0 text-xs tabular-nums text-gray-500">{{ __('dashboard.breakdown.resolved_share', ['percent' => $resolvedShare]) }}</span>
                    </header>
                    <dl class="flex flex-col gap-2.5">
                        @foreach($statusMeta as $meta)
                            <div class="grid grid-cols-[7rem_1fr_auto] items-center gap-2.5 text-[13.5px]">
                                <dt class="flex min-w-0 items-center gap-1.5 text-gray-500">
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ $meta['bar'] }}" aria-hidden="true"></span>
                                    <span class="truncate">{{ $meta['label'] }}</span>
                                </dt>
                                <span class="h-2 overflow-hidden rounded-full bg-gray-100" aria-hidden="true">
                                    <span class="block h-full rounded-full {{ $meta['bar'] }}" style="width: {{ $meta['count'] / $totalReports * 100 }}%"></span>
                                </span>
                                <dd class="w-6 text-end font-semibold tabular-nums text-gray-900">{{ $meta['count'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section class="{{ $module }}" aria-labelledby="category-breakdown-title">
                    <header class="flex items-baseline justify-between gap-3">
                        <h2 id="category-breakdown-title" class="text-base font-semibold leading-snug text-gray-900">{{ __('dashboard.breakdown.categories') }}</h2>
                    </header>
                    @if($sortedReportsByCategory->isNotEmpty())
                        <div class="flex flex-1 items-center gap-5">
                            <figure class="relative h-28 w-28 shrink-0">
                                <svg viewBox="0 0 36 36" class="h-full w-full" aria-hidden="true">
                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f3f4f6" stroke-width="4"></circle>
                                    @php $offset = 0; @endphp
                                    @foreach($categorySlices as $slice)
                                        @php $pct = $slice['count'] / $categoryTotal * 100; @endphp
                                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="{{ $slice['color'] }}" stroke-width="4"
                                                stroke-dasharray="{{ $pct }} {{ 100 - $pct }}" stroke-dashoffset="{{ 25 - $offset }}">
                                            <title>{{ $slice['label'] }} · {{ $slice['count'] }}</title>
                                        </circle>
                                        @php $offset += $pct; @endphp
                                    @endforeach
                                </svg>
                                <figcaption class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-xl font-bold leading-none tabular-nums text-gray-900">{{ $totalReports }}</span>
                                    <span class="mt-1 text-[10px] uppercase tracking-wider text-gray-500">{{ __('dashboard.breakdown.count') }}</span>
                                </figcaption>
                            </figure>
                            <dl class="flex min-w-0 flex-1 flex-col gap-2.5">
                                @foreach($categorySlices as $slice)
                                    <div class="grid grid-cols-[minmax(0,1fr)_auto_2.5rem] items-center gap-2 text-[13.5px]">
                                        <dt class="flex min-w-0 items-center gap-1.5 text-gray-500">
                                            <span class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $slice['color'] }}" aria-hidden="true"></span>
                                            <span class="truncate">{{ $slice['label'] }}</span>
                                        </dt>
                                        <dd class="text-end font-semibold tabular-nums text-gray-900">{{ $slice['count'] }}</dd>
                                        <dd class="text-end text-xs tabular-nums text-gray-400">{{ round($slice['count'] / $categoryTotal * 100) }}%</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('dashboard.labels.unknown') }}</p>
                    @endif
                </section>

                <section class="{{ $module }}" aria-labelledby="activity-title">
                    <header class="flex items-baseline justify-between gap-3">
                        <h2 id="activity-title" class="text-base font-semibold leading-snug text-gray-900">{{ __('dashboard.activity.title') }}</h2>
                        <span class="shrink-0 text-xs tabular-nums text-gray-500">{{ __('dashboard.activity.this_month', ['count' => $thisMonth]) }}</span>
                    </header>
                    <figure class="flex flex-1 flex-col gap-1.5">
                        <div class="flex min-h-28 flex-1 items-end gap-1.5 border-b border-gray-200">
                            @foreach($reportsByMonth as $i => $m)
                                <div class="group relative flex h-full flex-1 items-end" tabindex="0" aria-label="{{ $monthLabel($m['month']) }} {{ $m['month']->year }}: {{ $m['count'] }}">
                                    <div @class([
                                            'w-full rounded-t-[3px] transition-opacity group-hover:opacity-75',
                                            'bg-red-600' => $i === $currentIndex,
                                            'bg-red-200' => $i !== $currentIndex,
                                        ])
                                         style="height: {{ $m['count'] > 0 ? 'max(' . ($m['count'] / $maxMonth * 100) . '%, 3px)' : '0' }}"></div>
                                    <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1 hidden -translate-x-1/2 whitespace-nowrap rounded-md border border-gray-200 bg-white px-2 py-1 text-xs text-gray-700 shadow-md group-hover:block group-focus:block">
                                        <span class="font-semibold tabular-nums">{{ $monthLabel($m['month']) }} {{ $m['month']->year }}</span> · {{ $m['count'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex gap-1.5 text-[10px] tabular-nums text-gray-500" aria-hidden="true">
                            @foreach($reportsByMonth as $m)
                                <span class="flex-1 text-center">{{ $monthLabel($m['month']) }}</span>
                            @endforeach
                        </div>
                    </figure>
                </section>
            </div>

            <section class="mt-8" aria-labelledby="reports-title">
                <div class="mb-3 flex items-end justify-between gap-3">
                    <div>
                        <h2 id="reports-title" class="text-base font-semibold text-gray-900">{{ __('dashboard.table.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.table.subtitle') }}</p>
                    </div>
                    <span class="text-sm tabular-nums text-gray-500">{{ $totalReports }}</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                    <table class="w-full min-w-[640px] text-start">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/80">
                                <th scope="col" class="px-4 py-3 text-start text-[11px] font-medium uppercase tracking-wider text-gray-500 sm:px-5">{{ __('dashboard.table.headers.title') }}</th>
                                <th scope="col" class="px-4 py-3 text-start text-[11px] font-medium uppercase tracking-wider text-gray-500">{{ __('dashboard.table.headers.status') }}</th>
                                <th scope="col" class="px-4 py-3 text-start text-[11px] font-medium uppercase tracking-wider text-gray-500">{{ __('dashboard.table.headers.created') }}</th>
                                <th scope="col" class="px-4 py-3 text-end text-[11px] font-medium uppercase tracking-wider text-gray-500 sm:px-5">{{ __('dashboard.table.headers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reports as $report)
                                <x-dashboard.report-row :report="$report" />
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
