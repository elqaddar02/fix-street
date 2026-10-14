@php
    $title = __('nav.my_reports') . ' - Madinup';

    $statusMeta = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'count' => $openReports, 'bar' => 'bg-amber-500'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'count' => $inProgressReports, 'bar' => 'bg-sky-600'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'count' => $resolvedReports, 'bar' => 'bg-emerald-600'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'count' => $rejectedReports, 'bar' => 'bg-gray-400'],
    ];

    $sortedReportsByCategory = collect($reportsByCategory)->sortDesc();
    $maxCategory = $sortedReportsByCategory->max() ?: 1;
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-5 border-b border-gray-200 pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-gray-500">{{ __('nav.my_reports') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-[1.75rem]">
                    {{ __('dashboard.greeting', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1.5 text-sm text-gray-500">{{ __('dashboard.subtitle') }}</p>
            </div>

            <a href="{{ route('reports.create') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-md bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                {{ __('dashboard.new_report') }}
            </a>
        </header>

        <section aria-label="{{ __('dashboard.stats.total') }}" class="mt-8 grid grid-cols-2 overflow-hidden rounded-lg border border-gray-200 bg-white sm:grid-cols-4">
            @foreach([
                ['label' => __('dashboard.stats.total'), 'value' => $totalReports, 'dot' => null],
                ['label' => $statusMeta['OPEN']['label'], 'value' => $statusMeta['OPEN']['count'], 'dot' => $statusMeta['OPEN']['bar']],
                ['label' => $statusMeta['IN_PROGRESS']['label'], 'value' => $statusMeta['IN_PROGRESS']['count'], 'dot' => $statusMeta['IN_PROGRESS']['bar']],
                ['label' => $statusMeta['RESOLVED']['label'], 'value' => $statusMeta['RESOLVED']['count'], 'dot' => $statusMeta['RESOLVED']['bar']],
            ] as $stat)
                <article class="border-gray-200 px-5 py-4 max-sm:odd:border-e max-sm:[&:nth-child(n+3)]:border-t sm:border-e sm:last:border-e-0 sm:py-5">
                    <p class="flex items-center gap-2 text-xs font-medium text-gray-500 sm:text-sm">
                        @if($stat['dot'])<span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $stat['dot'] }}" aria-hidden="true"></span>@endif
                        <span class="truncate">{{ $stat['label'] }}</span>
                    </p>
                    <p class="mt-1.5 text-3xl font-semibold tabular-nums tracking-tight text-gray-900">{{ $stat['value'] }}</p>
                </article>
            @endforeach
        </section>

        @if($totalReports === 0)
            <section class="mt-8 rounded-lg border border-gray-200 bg-white px-6 py-14 text-center">
                <h2 class="text-base font-semibold text-gray-900">{{ __('dashboard.empty.headline') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ __('dashboard.empty.body') }}</p>
                <a href="{{ route('reports.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-md bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                    {{ __('dashboard.new_report') }}
                </a>
            </section>
        @else
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg border border-gray-200 bg-white p-5 sm:p-6" aria-labelledby="status-breakdown-title">
                    <h2 id="status-breakdown-title" class="text-sm font-semibold text-gray-900">{{ __('dashboard.breakdown.title') }}</h2>

                    <div class="mt-5 flex h-2 w-full overflow-hidden rounded-sm bg-gray-100" role="img" aria-label="{{ __('dashboard.breakdown.title') }}">
                        @foreach($statusMeta as $meta)
                            @if($meta['count'] > 0)
                                <div class="h-full {{ $meta['bar'] }}" style="width: {{ $meta['count'] / $totalReports * 100 }}%"></div>
                            @endif
                        @endforeach
                    </div>

                    <ul class="mt-5 divide-y divide-gray-100">
                        @foreach($statusMeta as $meta)
                            @if($meta['count'] > 0)
                                <li class="flex min-w-0 items-center justify-between gap-3 py-2.5 text-sm">
                                    <span class="flex min-w-0 items-center gap-2.5 text-gray-600"><span class="h-2 w-2 shrink-0 rounded-sm {{ $meta['bar'] }}" aria-hidden="true"></span><span class="truncate">{{ $meta['label'] }}</span></span>
                                    <span class="flex shrink-0 items-baseline gap-3">
                                        <span class="text-xs tabular-nums text-gray-400">{{ round($meta['count'] / $totalReports * 100) }}%</span>
                                        <span class="w-8 text-end font-medium tabular-nums text-gray-900">{{ $meta['count'] }}</span>
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white p-5 sm:p-6" aria-labelledby="category-breakdown-title">
                    <h2 id="category-breakdown-title" class="text-sm font-semibold text-gray-900">{{ __('dashboard.breakdown.categories') }}</h2>
                    @if($sortedReportsByCategory->isNotEmpty())
                        <ul class="mt-5 space-y-4">
                            @foreach($sortedReportsByCategory as $name => $count)
                                <li>
                                    <div class="flex items-center justify-between gap-3 text-sm">
                                        <span class="min-w-0 truncate text-gray-600">{{ $name ?: __('dashboard.labels.unknown') }}</span>
                                        <span class="shrink-0 font-medium tabular-nums text-gray-900">{{ $count }}</span>
                                    </div>
                                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-sm bg-gray-100">
                                        <div class="h-full rounded-sm bg-gray-800" style="width: {{ ($count / $maxCategory) * 100 }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-5 text-sm text-gray-500">{{ __('dashboard.labels.unknown') }}</p>
                    @endif
                </section>
            </div>

            <section class="mt-10" aria-labelledby="reports-title">
                <div class="mb-3 flex items-end justify-between gap-3">
                    <div>
                        <h2 id="reports-title" class="text-base font-semibold text-gray-900">{{ __('dashboard.table.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.table.subtitle') }}</p>
                    </div>
                    <span class="text-sm tabular-nums text-gray-500">{{ $totalReports }}</span>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
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
