@php
    $languages = ['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'];
@endphp

<details class="relative inline-block text-start">
    <summary class="inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500" title="{{ __('Change Language') }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>
        </svg>
        <span class="sr-only">{{ __('Change Language') }}</span>
    </summary>

    <div class="absolute end-0 z-50 mt-2 w-44 overflow-hidden rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200">
        @foreach($languages as $code => $name)
            <a href="{{ route('language.switch', $code) }}"
               @class([
                   'flex items-center gap-3 px-3 py-2 text-sm transition-colors hover:bg-gray-50',
                   'font-semibold text-gray-900' => app()->getLocale() === $code,
                   'text-gray-700' => app()->getLocale() !== $code,
               ])>
                <span class="inline-flex w-7 justify-center rounded-md bg-gray-100 py-0.5 text-[11px] font-semibold uppercase text-gray-600">{{ $code }}</span>
                <span>{{ $name }}</span>
                @if(app()->getLocale() === $code)
                    <svg class="ms-auto h-4 w-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                @endif
            </a>
        @endforeach
    </div>
</details>
