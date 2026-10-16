{{-- The pin mark only (≈20 KB). The old 1536×1024 artwork weighed 2.1 MB and
     carried the former "Madinova" wordmark; the name is now written as text. --}}
<img
    src="{{ asset('assets/logo-mark.png') }}"
    alt="{{ config('app.name', 'Madinup') }}"
    width="101" height="128"
    {{ $attributes->merge(['class' => 'h-10 w-auto']) }}
>
