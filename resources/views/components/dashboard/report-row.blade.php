@php
    $statusStyles = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'class' => 'border-amber-500 text-amber-800'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'class' => 'border-sky-600 text-sky-800'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'class' => 'border-emerald-600 text-emerald-800'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'class' => 'border-gray-400 text-gray-600'],
    ];
    $status = $statusStyles[$report->status] ?? $statusStyles['OPEN'];
    $city = $report->city->display_name ?? null;
    $hasImage = $report->image && file_exists(storage_path('app/public/' . $report->image));
@endphp

<tr class="transition-colors hover:bg-gray-50">
    <td class="whitespace-nowrap px-4 py-3 align-middle text-sm tabular-nums text-gray-500">#{{ str_pad((string) $report->id, 6, '0', STR_PAD_LEFT) }}</td>

    <td class="px-4 py-3 align-middle">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ route('reports.show', $report) }}" class="h-10 w-10 shrink-0 overflow-hidden rounded-sm border border-gray-300 bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b2a41]" aria-label="{{ $report->title }}">
                @if($hasImage)
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" loading="lazy" class="h-full w-full object-cover">
                @else
                    <span class="flex h-full w-full items-center justify-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 19V5a2 2 0 012-2h14a2 2 0 012 2v14M3 19l5-5 4 4 3-3 6 5M8.5 8.5h.01"/></svg>
                    </span>
                @endif
            </a>
            <div class="min-w-0">
                <a href="{{ route('reports.show', $report) }}" class="block max-w-sm truncate text-sm font-medium text-gray-900 hover:text-[#1b2a41] hover:underline focus:outline-none focus-visible:underline">{{ $report->title }}</a>
                <p class="mt-0.5 flex min-w-0 items-center gap-1.5 truncate text-xs text-gray-500">
                    <span class="truncate">{{ $city ?: __('dashboard.labels.unknown') }}</span>
                    <span aria-hidden="true" class="text-gray-300">|</span>
                    <span class="truncate">{{ $report->category->display_name ?? __('dashboard.labels.unknown') }}</span>
                </p>
            </div>
        </div>
    </td>

    <td class="whitespace-nowrap px-4 py-3 align-middle">
        <span class="inline-block rounded-sm border px-2 py-0.5 text-xs font-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
    </td>

    <td class="whitespace-nowrap px-4 py-3 align-middle">
        <time class="text-sm tabular-nums text-gray-700" datetime="{{ $report->created_at->toIso8601String() }}" title="{{ $report->created_at->format('d/m/Y H:i') }}">{{ $report->created_at->format('d/m/Y') }}</time>
    </td>

    <td class="whitespace-nowrap px-4 py-3 text-end align-middle">
        <div class="flex items-center justify-end gap-1">
            <a href="{{ route('reports.show', $report) }}" class="rounded-sm px-2.5 py-1.5 text-xs font-medium text-[#1b2a41] underline underline-offset-2 transition-colors hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b2a41]" title="{{ __('dashboard.actions.view') }}">
                {{ __('dashboard.actions.view') }}
            </a>
            <a href="{{ route('reports.edit', $report) }}" class="rounded-sm p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1b2a41]" title="{{ __('dashboard.actions.edit') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828z"/></svg>
                <span class="sr-only">{{ __('dashboard.actions.edit') }}</span>
            </a>
            <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit='return confirm({{ json_encode(__('dashboard.confirm.delete_report')) }})' class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-sm p-2 text-gray-500 transition-colors hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-600" title="{{ __('dashboard.actions.delete') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span class="sr-only">{{ __('dashboard.actions.delete') }}</span>
                </button>
            </form>
        </div>
    </td>
</tr>
