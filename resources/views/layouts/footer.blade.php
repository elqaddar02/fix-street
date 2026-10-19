@php
    $columns = [
        __('footer.navigation') => [
            [route('home'), __('footer.home')],
            [route('reports.index'), __('footer.view_reports')],
            ...(auth()->user()?->is_admin ? [] : [[route('reports.create'), __('dashboard.new_report')]]),
        ],
        __('footer.resources') => [
            [route('help'), __('footer.help')],
            [route('about'), __('footer.about')],
            [route('contact'), __('footer.contact')],
        ],
        __('footer.account') => auth()->check()
            ? [
                [route('dashboard'), __('nav.my_reports')],
                [route('profile.edit'), __('footer.my_profile')],
            ]
            : [
                [route('login'), __('Log in')],
                [route('register'), __('Register')],
            ],
    ];
@endphp

{{-- Light, like the rest of the site: the logo's dark lettering also
     disappeared on the old near-black footer. --}}
<footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 rounded-lg">
                    <x-application-logo class="h-10 w-auto" />
                    <span class="text-lg font-semibold tracking-tight text-gray-900">{{ config('app.name', 'Madinup') }}</span>
                </a>
                <p class="mt-3 max-w-sm text-sm leading-6 text-gray-500">{{ __('footer.tagline') }}</p>
                <p class="mt-4 flex max-w-sm items-start gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs leading-5 text-gray-600">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                    <span>{{ __('footer.independent') }}</span>
                </p>
            </div>

            <nav aria-label="{{ __('footer.navigation') }}" class="grid grid-cols-2 gap-x-6 gap-y-8 sm:grid-cols-3 lg:col-span-7">
                @foreach($columns as $heading => $links)
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-900">{{ $heading }}</h2>
                        <ul class="mt-3 space-y-1">
                            @foreach($links as [$href, $linkLabel])
                                <li>
                                    <a href="{{ $href }}" class="inline-block py-1 text-sm text-gray-600 transition-colors hover:text-red-700">{{ $linkLabel }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-gray-200 pt-6 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Madinup') }}. {{ __('footer.all_rights_reserved') }}</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('privacy') }}" class="py-1 transition-colors hover:text-gray-900">{{ __('footer.privacy_policy') }}</a>
                <a href="{{ route('terms') }}" class="py-1 transition-colors hover:text-gray-900">{{ __('footer.terms_of_service') }}</a>
            </div>
        </div>
    </div>
</footer>
