@php
    $title = __('nav.my_reports') . ' - Madinup';

    $statusMeta = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'count' => $openReports, 'bar' => 'bg-amber-400', 'dot' => 'bg-amber-400'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'count' => $inProgressReports, 'bar' => 'bg-sky-500', 'dot' => 'bg-sky-500'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'count' => $resolvedReports, 'bar' => 'bg-emerald-500', 'dot' => 'bg-emerald-500'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'count' => $rejectedReports, 'bar' => 'bg-rose-400', 'dot' => 'bg-rose-400'],
    ];

    $topCategory = collect($reportsByCategory)->sortDesc()->keys()->first();
    $maxCategory = collect($reportsByCategory)->max() ?: 1;
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{--
            The old page opened with a full-bleed gradient hero plus a card whose
            only content was the "add report" button, so the primary action sat
            below the fold. Greeting and action now share one compact row.
        --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    {{ __('dashboard.greeting', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.subtitle') }}</p>
            </div>

            <a href="{{ route('reports.create') }}"
               class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('dashboard.new_report') }}
            </a>
        </div>

        @if($totalReports === 0)
            {{--
                Nothing to summarise yet. Charts of zero and a table of nothing
                only make an empty account look broken, so the first run is a
                single clear invitation instead.
            --}}
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50">
                    <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('dashboard.empty.headline') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">{{ __('dashboard.empty.body') }}</p>
                <a href="{{ route('reports.create') }}"
                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('dashboard.empty.action') }}
                </a>
            </div>
        @else
            {{-- Compact counters rather than four large gradient tiles. --}}
            <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                    <p class="text-xs font-medium text-gray-500">{{ __('dashboard.stats.total') }}</p>
                    <p class="mt-0.5 text-2xl font-bold tabular-nums text-gray-900">{{ $totalReports }}</p>
                </div>
                @foreach(['OPEN', 'IN_PROGRESS', 'RESOLVED'] as $key)
                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                        <p class="flex items-center gap-1.5 text-xs font-medium text-gray-500">
                            <span class="h-1.5 w-1.5 rounded-full {{ $statusMeta[$key]['dot'] }}"></span>
                            {{ $statusMeta[$key]['label'] }}
                        </p>
                        <p class="mt-0.5 text-2xl font-bold tabular-nums text-gray-900">{{ $statusMeta[$key]['count'] }}</p>
                    </div>
                @endforeach
            </div>

            {{--
                A donut and a bar chart for a handful of personal reports was more
                chrome than signal. A single proportional bar answers "where do my
                reports stand?" at a glance, needs no chart library, and reads
                correctly whether there is one report or fifty.
            --}}
            <div class="mb-6 grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('dashboard.breakdown.title') }}</h2>

                    <div class="mt-4 flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                        @foreach($statusMeta as $meta)
                            @if($meta['count'] > 0)
                                <div class="{{ $meta['bar'] }} transition-all"
                                     style="width: {{ ($meta['count'] / $totalReports) * 100 }}%"
                                     title="{{ $meta['label'] }}: {{ $meta['count'] }}"></div>
                            @endif
                        @endforeach
                    </div>

                    <ul class="mt-4 space-y-2">
                        @foreach($statusMeta as $meta)
                            @if($meta['count'] > 0)
                                <li class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 text-gray-600">
                                        <span class="h-2 w-2 rounded-full {{ $meta['dot'] }}"></span>
                                        {{ $meta['label'] }}
                                    </span>
                                    <span class="font-semibold tabular-nums text-gray-900">
                                        {{ __('dashboard.of_total', ['count' => $meta['count'], 'total' => $totalReports]) }}
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Only worth its space once there is more than one category to compare. --}}
                @if(count($reportsByCategory) > 1)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold text-gray-900">{{ __('dashboard.breakdown.categories') }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach($reportsByCategory as $name => $count)
                                <li>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="truncate pr-3 text-gray-600">{{ $name ?: __('dashboard.labels.unknown') }}</span>
                                        <span class="font-semibold tabular-nums text-gray-900">{{ $count }}</span>
                                    </div>
                                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full bg-red-500" style="width: {{ ($count / $maxCategory) * 100 }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif($topCategory)
                    {{-- A single category needs a sentence, not a chart of one bar. --}}
                    <div class="flex flex-col justify-center rounded-2xl border border-gray-200 bg-white p-5">
                        <h2 class="text-sm font-semibold text-gray-900">{{ __('dashboard.breakdown.categories') }}</h2>
                        <p class="mt-2 text-sm text-gray-500">
                            <span class="font-semibold text-gray-900">{{ $topCategory }}</span>
                            — {{ __('dashboard.of_total', ['count' => $reportsByCategory[$topCategory], 'total' => $totalReports]) }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Reports --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3.5">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('dashboard.table.title') }}</h2>
                    <span class="text-xs text-gray-500">{{ $totalReports }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th scope="col" class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('dashboard.table.headers.title') }}</th>
                                <th scope="col" class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('dashboard.table.headers.status') }}</th>
                                <th scope="col" class="px-5 py-2.5 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('dashboard.table.headers.created') }}</th>
                                <th scope="col" class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('dashboard.table.headers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <x-dashboard.report-row :report="$report" />
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
