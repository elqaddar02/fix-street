@php
    // Static class strings: Tailwind cannot generate interpolated names.
    $statusStyles = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'class' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'class' => 'bg-sky-50 text-sky-700 ring-sky-600/20'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'class' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
    ];
    $status = $statusStyles[$report->status] ?? $statusStyles['OPEN'];
@endphp

<tr class="border-b border-gray-100 last:border-b-0 transition-colors hover:bg-gray-50/70">
    <td class="px-5 py-3.5">
        <a href="{{ route('reports.show', $report) }}" class="block max-w-xs">
            <span class="block truncate text-sm font-medium text-gray-900 hover:text-red-600">{{ $report->title }}</span>
            <span class="block truncate text-xs text-gray-500">
                {{ $report->category->display_name ?? __('dashboard.labels.unknown') }}
                · {{ $report->city->display_name ?? __('dashboard.labels.unknown') }}
            </span>
        </a>
    </td>

    <td class="px-5 py-3.5 whitespace-nowrap">
        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $status['class'] }}">
            {{ $status['label'] }}
        </span>
    </td>

    <td class="px-5 py-3.5 whitespace-nowrap">
        <span class="text-sm text-gray-600" title="{{ $report->created_at->format('d/m/Y H:i') }}">
            {{ $report->created_at->diffForHumans() }}
        </span>
    </td>

    <td class="px-5 py-3.5 whitespace-nowrap">
        <div class="flex items-center justify-end gap-1">
            <a href="{{ route('reports.show', $report) }}"
               class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700"
               title="{{ __('dashboard.actions.view') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span class="sr-only">{{ __('dashboard.actions.view') }}</span>
            </a>

            <a href="{{ route('reports.edit', $report) }}"
               class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700"
               title="{{ __('dashboard.actions.edit') }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span class="sr-only">{{ __('dashboard.actions.edit') }}</span>
            </a>

            <form method="POST" action="{{ route('reports.destroy', $report) }}"
                  onsubmit='return confirm({{ json_encode(__('dashboard.confirm.delete_report')) }})'
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                        title="{{ __('dashboard.actions.delete') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span class="sr-only">{{ __('dashboard.actions.delete') }}</span>
                </button>
            </form>
        </div>
    </td>
</tr>
