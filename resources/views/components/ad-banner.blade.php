@props([
    'type' => 'horizontal',
])

@php
    $isSquare = $type === 'square';

    $containerClasses = $isSquare
        ? 'mx-auto w-full max-w-xs'
        : 'w-full';

    $placeholderClasses = $isSquare
        ? 'aspect-square'
        : 'h-24 sm:h-28 md:h-32';

    $label = $isSquare ? 'Ad Placeholder (Square)' : 'Ad Placeholder (Horizontal)';
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <div class="flex {{ $placeholderClasses }} items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-100 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
        {{ $label }}
    </div>
</div>
