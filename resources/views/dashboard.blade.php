@php
    $title = __('nav.my_reports') . ' - Madinup';

    $statusMeta = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'count' => $openReports],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'count' => $inProgressReports],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'count' => $resolvedReports],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'count' => $rejectedReports],
    ];

    $sortedReportsByCategory = collect($reportsByCategory)->sortDesc();

    $percent = fn (int $count) => $totalReports > 0 ? round($count / $totalReports * 100) . '%' : '–';

    $panel = 'border border-gray-300 bg-white';
    $panelHead = 'border-b border-gray-300 bg-gray-100 px-4 py-2.5 font-serif text-base font-semibold text-[#1b2a41]';
    $th = 'px-4 py-2.5 text-start text-xs font-semibold text-gray-600';
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-5 border-b-2 border-[#1b2a41] pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <h1 class="font-serif text-2xl font-semibold text-[#1b2a41] sm:text-3xl">
                    {{ __('dashboard.greeting', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1.5 text-sm text-gray-600">{{ __('dashboard.subtitle') }}</p>
            </div>

            <a href="{{ route('reports.create') }}"
               class="inline-flex shrink-0 items-center justify-center gap-2 rounded-sm bg-[#1b2a41] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#122033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b2a41] focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                {{ __('dashboard.new_report') }}
            </a>
        </header>

        @if($totalReports === 0)
            <section class="{{ $panel }} mt-8 px-6 py-12 text-center">
                <h2 class="font-serif text-lg font-semibold text-[#1b2a41]">{{ __('dashboard.empty.headline') }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600">{{ __('dashboard.empty.body') }}</p>
                <a href="{{ route('reports.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-sm bg-[#1b2a41] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#122033] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b2a41] focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                    {{ __('dashboard.new_report') }}
                </a>
            </section>
        @else
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <section class="{{ $panel }}" aria-labelledby="status-breakdown-title">
                    <h2 id="status-breakdown-title" class="{{ $panelHead }}">{{ __('dashboard.breakdown.title') }}</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th scope="col" class="{{ $th }}">{{ __('dashboard.table.headers.status') }}</th>
                                <th scope="col" class="{{ $th }} text-end">{{ __('dashboard.breakdown.count') }}</th>
                                <th scope="col" class="{{ $th }} w-16 text-end">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($statusMeta as $meta)
                                <tr>
                                    <td class="px-4 py-2.5 text-gray-700">{{ $meta['label'] }}</td>
                                    <td class="px-4 py-2.5 text-end tabular-nums text-gray-900">{{ $meta['count'] }}</td>
                                    <td class="px-4 py-2.5 text-end tabular-nums text-gray-500">{{ $percent($meta['count']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 bg-gray-50 font-semibold">
                                <th scope="row" class="px-4 py-2.5 text-start text-gray-900">{{ __('dashboard.stats.total') }}</th>
                                <td class="px-4 py-2.5 text-end tabular-nums text-gray-900">{{ $totalReports }}</td>
                                <td class="px-4 py-2.5 text-end tabular-nums text-gray-500">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </section>

                <section class="{{ $panel }}" aria-labelledby="category-breakdown-title">
                    <h2 id="category-breakdown-title" class="{{ $panelHead }}">{{ __('dashboard.breakdown.categories') }}</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th scope="col" class="{{ $th }}">{{ __('dashboard.table.headers.category') }}</th>
                                <th scope="col" class="{{ $th }} text-end">{{ __('dashboard.breakdown.count') }}</th>
                                <th scope="col" class="{{ $th }} w-16 text-end">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sortedReportsByCategory as $name => $count)
                                <tr>
                                    <td class="max-w-[14rem] truncate px-4 py-2.5 text-gray-700">{{ $name ?: __('dashboard.labels.unknown') }}</td>
                                    <td class="px-4 py-2.5 text-end tabular-nums text-gray-900">{{ $count }}</td>
                                    <td class="px-4 py-2.5 text-end tabular-nums text-gray-500">{{ $percent($count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>

            <section class="mt-10" aria-labelledby="reports-title">
                <div class="mb-3 flex items-end justify-between gap-3 border-b border-gray-300 pb-2">
                    <div>
                        <h2 id="reports-title" class="font-serif text-xl font-semibold text-[#1b2a41]">{{ __('dashboard.table.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('dashboard.table.subtitle') }}</p>
                    </div>
                    <span class="text-sm tabular-nums text-gray-600">{{ $totalReports }}</span>
                </div>

                <div class="{{ $panel }} overflow-x-auto">
                    <table class="w-full min-w-[640px] text-start">
                        <thead>
                            <tr class="border-b-2 border-gray-300 bg-gray-100">
                                <th scope="col" class="{{ $th }} w-24">{{ __('dashboard.table.headers.reference') }}</th>
                                <th scope="col" class="{{ $th }}">{{ __('dashboard.table.headers.title') }}</th>
                                <th scope="col" class="{{ $th }}">{{ __('dashboard.table.headers.status') }}</th>
                                <th scope="col" class="{{ $th }}">{{ __('dashboard.table.headers.created') }}</th>
                                <th scope="col" class="{{ $th }} text-end">{{ __('dashboard.table.headers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
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
