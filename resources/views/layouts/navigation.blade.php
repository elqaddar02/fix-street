@php
    // One source of truth for both the desktop bar and the mobile sheet, so
    // the two can never drift apart.
    $links = [
        ['route' => 'home', 'active' => request()->routeIs('home'), 'label' => __('nav.home'), 'icon' => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
    ];

    if (auth()->check()) {
        // Admins don't file reports: their own space is the admin panel.
        $isAdmin = (bool) auth()->user()->is_admin;
        $links[] = [
            'route' => $isAdmin ? 'admin.dashboard' : 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'label' => $isAdmin ? __('nav.admin_panel') : __('nav.my_reports'),
            'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
        ];
    }

    $links[] = ['route' => 'reports.index', 'active' => request()->routeIs('reports.index'), 'label' => __('nav.all_reports'), 'icon' => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'];
    $links[] = ['route' => 'help', 'active' => request()->routeIs('help'), 'label' => __('nav.help'), 'icon' => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z'];
@endphp

{{--
    x-data lives on the <nav> itself. It used to be missing entirely, which
    meant `open` had no Alpine scope: the ☰ button did nothing and the mobile
    panel rendered permanently expanded because its :class never evaluated.
--}}
<nav x-data="{ open: false }" @keydown.escape.window="open = false"
     class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- LOGO --}}
            <a href="{{ route('home') }}"
               aria-label="{{ __('nav.home') }}"
               class="flex shrink-0 items-center gap-2 rounded-lg px-1.5 py-1 transition-colors hover:bg-gray-50">
                <x-application-logo class="h-9 w-auto" />
                <span class="text-lg font-semibold tracking-tight text-gray-900">{{ config('app.name', 'Madinup') }}</span>
            </a>

            {{-- CENTER NAV --}}
            <div class="hidden flex-1 justify-center lg:flex">
                <div class="flex items-center gap-1 rounded-xl border border-gray-200 bg-gray-50/80 p-1">
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           @if($link['active']) aria-current="page" @endif
                           @class([
                               'flex h-9 items-center gap-2 rounded-lg px-3.5 text-sm font-medium transition-colors',
                               'bg-white text-red-700 shadow-sm ring-1 ring-gray-200' => $link['active'],
                               'text-gray-600 hover:bg-white hover:text-gray-900' => ! $link['active'],
                           ])>
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT SIDE (desktop) --}}
            <div class="hidden shrink-0 items-center gap-3 lg:flex">
                <x-language-switcher />

                @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-red-50 text-xs font-semibold text-red-700">
                                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                                </span>
                                <span class="max-w-[10rem] truncate font-medium">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="border-b border-gray-100 px-4 py-3">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('nav.my_reports') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(Auth::user()->is_admin)
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('nav.admin_panel') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}"
                           class="flex h-10 items-center rounded-lg px-4 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}"
                           class="flex h-10 items-center rounded-lg bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endauth
            </div>

            {{-- MOBILE TOGGLE --}}
            <button type="button"
                    @click="open = ! open"
                    :aria-expanded="open ? 'true' : 'false'"
                    aria-controls="mobile-menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 transition-colors hover:bg-gray-100 lg:hidden">
                <span class="sr-only" x-text="open ? '{{ __('nav.close_menu') }}' : '{{ __('nav.open_menu') }}'">{{ __('nav.open_menu') }}</span>
                <svg x-show="! open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu"
         x-show="open"
         x-cloak
         x-transition
         @click.outside="open = false"
         class="max-h-[calc(100vh-4rem)] overflow-y-auto border-t border-gray-200 bg-white lg:hidden">

        <div class="space-y-1 px-4 py-4">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   @if($link['active']) aria-current="page" @endif
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium transition-colors',
                       'bg-red-50 text-red-700' => $link['active'],
                       'text-gray-700 hover:bg-gray-50' => ! $link['active'],
                   ])>
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        {{--
            The account controls used to be desktop-only (hidden sm:flex), so a
            phone user had no way to reach their profile, log out, or sign in.
        --}}
        <div class="border-t border-gray-200 px-4 py-4">
            @auth
                <div class="mb-3 flex items-center gap-3 px-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-sm font-semibold text-red-700">
                        {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        {{ __('Profile') }}
                    </a>

                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                            {{ __('nav.admin_panel') }}
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-start text-base font-medium text-red-700 transition-colors hover:bg-red-50">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                       class="flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-base font-semibold text-white transition-colors hover:bg-red-700">
                        {{ __('Register') }}
                    </a>
                </div>
            @endauth

            {{-- Inline choices rather than the desktop dropdown, which opened
                 off the edge of the screen inside this panel. --}}
            <div class="mt-3 border-t border-gray-100 pt-3">
                <p class="px-1 text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Change Language') }}</p>
                <div class="mt-2 grid grid-cols-3 gap-2">
                    @foreach(['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'] as $code => $languageName)
                        <a href="{{ route('language.switch', $code) }}" @if(app()->getLocale() === $code) aria-current="true" @endif
                           @class([
                               'flex h-10 items-center justify-center rounded-lg border text-sm font-medium transition-colors',
                               'border-red-200 bg-red-50 text-red-700' => app()->getLocale() === $code,
                               'border-gray-200 text-gray-700 hover:bg-gray-50' => app()->getLocale() !== $code,
                           ])>{{ $languageName }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</nav>
