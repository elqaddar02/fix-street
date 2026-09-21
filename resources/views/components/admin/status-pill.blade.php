@props(['status'])

@php
    // Static class strings — Tailwind cannot see interpolated names.
    $styles = [
        'OPEN' => ['label' => 'Ouvert', 'class' => 'bg-amber-50 text-amber-700 ring-amber-600/20'],
        'IN_PROGRESS' => ['label' => 'En cours', 'class' => 'bg-sky-50 text-sky-700 ring-sky-600/20'],
        'RESOLVED' => ['label' => 'Résolu', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
        'REJECTED' => ['label' => 'Rejeté', 'class' => 'bg-rose-50 text-rose-700 ring-rose-600/20'],
    ];

    $style = $styles[$status] ?? ['label' => $status, 'class' => 'bg-slate-50 text-slate-600 ring-slate-500/20'];
@endphp

<span {{ $attributes->class(['inline-flex shrink-0 items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset', $style['class']]) }}>
    {{ $style['label'] }}
</span>
