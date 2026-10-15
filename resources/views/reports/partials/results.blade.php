@if($reports->isNotEmpty())
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($reports as $report)
            <x-report-card :report="$report" />
        @endforeach
    </div>

    <div class="pt-6">
        {{ $reports->links() }}
    </div>
@else
    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <p class="mt-3 text-base font-semibold text-gray-900">{{ __('reports.empty.title') }}</p>
        <p class="mt-1 text-sm text-gray-500">{{ __('reports.empty.body') }}</p>
    </div>
@endif
