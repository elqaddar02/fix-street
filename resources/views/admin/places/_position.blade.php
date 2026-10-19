{{-- Whether a place has a map point. Without one, the automatic location never picks it. --}}
@if($lat !== null && $lng !== null)
    <span class="inline-flex items-center gap-1 text-slate-500" title="{{ number_format((float) $lat, 5) }}, {{ number_format((float) $lng, 5) }}">
        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
        {{ __('admin.on_map') }}
    </span>
@else
    <span class="inline-flex items-center gap-1 font-medium text-amber-700" title="{{ __('admin.no_position_hint') }}">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
        {{ __('admin.no_position') }}
    </span>
@endif
