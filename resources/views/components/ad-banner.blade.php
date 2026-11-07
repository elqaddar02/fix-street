@props([
    'placement' => 'home_top',
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

    $ad = \App\Models\Ad::eligible()
        ->forPlacement($placement)
        ->inRandomOrder()
        ->first();

    if ($ad) {
        $ad->events()->create([
            'type' => \App\Models\AdEvent::TYPE_IMPRESSION,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
@endphp

@if($ad)
    <div {{ $attributes->merge(['class' => $containerClasses]) }}>
        @if($ad->provider === \App\Models\Ad::PROVIDER_HOUSE && $ad->image)
            <a href="{{ route('ads.click', $ad) }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md">
                <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="{{ $placeholderClasses }} w-full object-cover">
            </a>
        @elseif($ad->provider === \App\Models\Ad::PROVIDER_ADSENSE && $ad->external_slot_id)
            <div class="flex {{ $placeholderClasses }} items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-400">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="{{ config('services.adsense.client_id', '') }}"
                     data-ad-slot="{{ $ad->external_slot_id }}"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
            </div>
        @endif
    </div>
@elseif(! app()->environment('production'))
    <div {{ $attributes->merge(['class' => $containerClasses]) }}>
        <div class="flex {{ $placeholderClasses }} items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-100 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
            {{ $isSquare ? 'Ad Placeholder (Square)' : 'Ad Placeholder (Horizontal)' }}
        </div>
    </div>
@endif
