<!-- Premium Report Row Component -->
<tr class="group hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent border-b border-gray-100 last:border-b-0 transition-all duration-200">
    <td class="px-8 py-6 whitespace-nowrap">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-900 group-hover:text-red-600 transition-colors duration-200">{{ Str::limit($report->title, 35) }}</div>
                <div class="text-xs text-gray-500">{{ __('dashboard.labels.id') }}: {{ $report->id }}</div>
            </div>
        </div>
    </td>

    <td class="px-8 py-6 whitespace-nowrap">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900">{{ $report->category->display_name ?? __('dashboard.labels.unknown') }}</span>
        </div>
    </td>

    <td class="px-8 py-6 whitespace-nowrap">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900">{{ $report->city->display_name ?? __('dashboard.labels.unknown') }}</span>
        </div>
    </td>

    <td class="px-8 py-6 whitespace-nowrap">
        @php
            $statusConfig = [
                'OPEN' => ['bg' => 'bg-gradient-to-r from-yellow-400 to-yellow-500', 'text' => 'text-yellow-900', 'icon' => 'clock'],
                'IN_PROGRESS' => ['bg' => 'bg-gradient-to-r from-orange-400 to-orange-500', 'text' => 'text-orange-900', 'icon' => 'bolt'],
                'RESOLVED' => ['bg' => 'bg-gradient-to-r from-green-400 to-green-500', 'text' => 'text-green-900', 'icon' => 'check-circle'],
                'CLOSED' => ['bg' => 'bg-gradient-to-r from-gray-400 to-gray-500', 'text' => 'text-gray-900', 'icon' => 'x-circle'],
            ];
            $config = $statusConfig[$report->status] ?? $statusConfig['OPEN'];
            $statusLabel = [
                'OPEN' => __('dashboard.status.open'),
                'IN_PROGRESS' => __('dashboard.status.in_progress'),
                'RESOLVED' => __('dashboard.status.resolved'),
                'CLOSED' => __('dashboard.status.closed'),
            ][$report->status] ?? __('dashboard.status.open');
        @endphp
        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full {{ $config['bg'] }} {{ $config['text'] }} shadow-sm">
            @if($config['icon'] === 'clock')
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @elseif($config['icon'] === 'bolt')
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            @elseif($config['icon'] === 'check-circle')
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @else
                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
            {{ $statusLabel }}
        </span>
    </td>

    <td class="px-8 py-6 whitespace-nowrap">
        <div class="text-sm text-gray-900 font-medium">{{ $report->created_at->format('M j, Y') }}</div>
        <div class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</div>
    </td>

    <td class="px-8 py-6 whitespace-nowrap">
        <div class="flex items-center space-x-1">
            <a href="{{ route('reports.show', $report) }}"
               class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-all duration-200 hover:scale-105">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>








                </svg>
                {{ __('dashboard.actions.view') }}
            </a>

            <a href="{{ route('reports.edit', $report) }}"
               class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-100 hover:bg-indigo-200 rounded-lg transition-all duration-200 hover:scale-105">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('dashboard.actions.edit') }}
            </a>

            <form method="POST" action="{{ route('reports.destroy', $report) }}"
                  onsubmit='return confirm({{ json_encode(__('dashboard.confirm.delete_report')) }})'
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-all duration-200 hover:scale-105">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{ __('dashboard.actions.delete') }}
                </button>
            </form>
        </div>
    </td>
</tr>