@props(['status'])

@php
    $styles = [
        'OPEN' => ['label' => __('dashboard.status.open'), 'class' => 'bg-amber-50 text-amber-800 ring-amber-600/20', 'dot' => 'bg-amber-500'],
        'IN_PROGRESS' => ['label' => __('dashboard.status.in_progress'), 'class' => 'bg-sky-50 text-sky-800 ring-sky-600/20', 'dot' => 'bg-sky-600'],
        'RESOLVED' => ['label' => __('dashboard.status.resolved'), 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20', 'dot' => 'bg-emerald-600'],
        'REJECTED' => ['label' => __('dashboard.status.rejected'), 'class' => 'bg-gray-100 text-gray-700 ring-gray-500/20', 'dot' => 'bg-gray-400'],
    ];
    $style = $styles[$status] ?? $styles['OPEN'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset ' . $style['class']]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $style['dot'] }}" aria-hidden="true"></span>
    {{ $style['label'] }}
</span>
