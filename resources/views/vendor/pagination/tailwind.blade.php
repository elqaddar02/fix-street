{{--
    Replaces Laravel's default pagination view: whole-sentence translation keys
    (the default glues "Showing", "to", "of", "results" together, which no
    Arabic or French sentence survives), the site's colours, and logical
    start/end spacing so it mirrors in RTL.
--}}
@if ($paginator->hasPages())
    @php
        $link = 'inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-sm font-medium transition-colors';
        $idle = $link . ' text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500';
        $off = $link . ' cursor-default text-slate-300';
        $prevIcon = 'M15.75 19.5 8.25 12l7.5-7.5';
        $nextIcon = 'm8.25 4.5 7.5 7.5-7.5 7.5';
    @endphp

    <nav role="navigation" aria-label="{{ __('pagination.label') }}" class="flex items-center justify-between gap-3">
        <p class="text-sm text-slate-500">
            @if ($paginator->firstItem())
                {{ __('pagination.summary', ['first' => $paginator->firstItem(), 'last' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
            @else
                {{ __('pagination.page_of', ['page' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}
            @endif
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="{{ $off }}" aria-disabled="true">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $prevIcon }}"/></svg>
                    <span class="sr-only">{{ __('pagination.previous') }}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $idle }}">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $prevIcon }}"/></svg>
                    <span class="sr-only">{{ __('pagination.previous') }}</span>
                </a>
            @endif

            {{-- Page numbers: hidden on phones, where the summary already says where you are. --}}
            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="{{ $off }}" aria-disabled="true">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="{{ $link }} bg-red-50 font-semibold text-red-700" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $idle }}" aria-label="{{ __('pagination.go_to', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $idle }}">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $nextIcon }}"/></svg>
                    <span class="sr-only">{{ __('pagination.next') }}</span>
                </a>
            @else
                <span class="{{ $off }}" aria-disabled="true">
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $nextIcon }}"/></svg>
                    <span class="sr-only">{{ __('pagination.next') }}</span>
                </span>
            @endif
        </div>
    </nav>
@endif
