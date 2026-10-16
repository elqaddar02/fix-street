@php
    $hasImage = $report->image && file_exists(storage_path('app/public/' . $report->image));
    $confirmations = $report->likes_count ?? 0;
@endphp
<a href="{{ route('reports.show', $report) }}" class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)] transition hover:border-gray-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 {{ $cardClass ?? '' }}">
    <div class="relative aspect-[16/9] overflow-hidden bg-gray-100">
        @if($hasImage)
            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
            </div>
        @endif
        <x-status-badge :status="$report->status" class="absolute end-3 top-3 shadow-sm" />
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-3 text-xs">
            <p class="flex min-w-0 items-center gap-1.5 text-gray-500">
                <span class="truncate font-medium text-red-700">{{ $report->category->display_name ?? __('dashboard.labels.unknown') }}</span>
                <span class="text-gray-300" aria-hidden="true">·</span>
                <span class="truncate">{{ $report->city->display_name ?? __('dashboard.labels.unknown') }}</span>
            </p>
            <time class="shrink-0 text-gray-500" datetime="{{ $report->created_at->toIso8601String() }}">{{ $report->created_at->copy()->locale(app()->getLocale())->translatedFormat('j M Y') }}</time>
        </div>

        {{-- title and description hold the same sentence (the form
             collects one field), so only show description when it differs. --}}
        <h3 class="mt-2 line-clamp-2 text-base font-semibold leading-snug text-gray-900 group-hover:text-red-700">{{ $report->title }}</h3>
        @if(filled($report->description) && $report->description !== $report->title)
            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $report->description }}</p>
        @endif

        <div class="min-h-4 flex-1" aria-hidden="true"></div>
        <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-3">
            <div class="flex min-w-0 items-center gap-2">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-50 text-[11px] font-semibold text-red-700">{{ mb_substr($report->user->name ?? 'A', 0, 1) }}</span>
                <span class="truncate text-xs font-medium text-gray-700">{{ $report->user->name ?? 'Anonymous' }}</span>
            </div>
            {{-- Was a comment count, which is meaningless now that
                 comments are off. Confirmations are the useful signal. --}}
            <span class="inline-flex shrink-0 items-center gap-1 text-xs text-gray-500"
                  title="{{ trans_choice('report.confirmations_count', $confirmations, ['count' => $confirmations]) }}">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span class="tabular-nums">{{ $confirmations }}</span>
            </span>
        </div>
    </div>
</a>
