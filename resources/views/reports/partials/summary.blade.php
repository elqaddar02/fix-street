{{ trans_choice('reports.summary', $reports->total(), ['count' => $reports->total()]) }}
@if($newToday > 0)
    <span class="text-gray-300" aria-hidden="true">·</span>
    <span class="font-medium text-red-700">{{ __('reports.new_today', ['count' => $newToday]) }}</span>
@endif
