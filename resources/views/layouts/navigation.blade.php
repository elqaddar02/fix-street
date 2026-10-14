@php
    // One source of truth for both the desktop bar and the mobile sheet, so
    // the two can never drift apart.
    $links = [
        ['route' => 'home', 'active' => request()->routeIs('home'), 'label' => __('nav.home'), 'icon' => '🏠'],
    ];

    if (auth()->check()) {
        $links[] = [
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'label' => __('nav.my_reports'),
            'icon' => '📌',
        ];
    }

    $links[] = ['route' => 'reports.index', 'active' => request()->routeIs('reports.index'), 'label' => __('nav.all_reports'), 'icon' => '📄'];
    $links[] = ['route' => 'help', 'active' => request()->routeIs('help'), 'label' => __('nav.help'), 'icon' => '❓'];
@endphp

{{--
    x-data lives on the <nav> itself. It used to be missing entirely, which
    meant `open` had no Alpine scope: the ☰ button did nothing and the mobile
    panel rendered permanently expanded because its :class never evaluated.
--}}
<nav x-data="{ open: false }" @keydown.escape.window="open = false"
     class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between gap-4">

            {{-- LOGO --}}
            <a href="{{ route('home') }}"
               aria-label="{{ __('nav.home') }}"
               class="flex shrink-0 items-center rounded-xl px-2 py-1 transition-colors hover:bg-gray-50">
                <span class="flex h-14 items-center">
                    <x-application-logo class="h-full w-auto" />
                </span>
            </a>

            {{-- CENTER NAV --}}
            <div class="hidden flex-1 justify-center lg:flex">
                <div class="flex items-center gap-1 rounded-full border border-gray-200 bg-white/70 px-1.5 py-1 shadow-sm backdrop-blur-md">
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           @if($link['active']) aria-current="page" @endif
                           @class([
                               'flex h-11 items-center gap-2 rounded-full px-5 text-sm font-medium transition-all duration-200',
                               'bg-red-600 text-white shadow-sm' => $link['active'],
                               'text-gray-700 hover:bg-gray-100' => ! $link['active'],
                           ])>
                            <span aria-hidden="true">{{ $link['icon'] }}</span>
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
                            <button class="flex h-11 items-center gap-2 rounded-full border border-gray-200 bg-white/70 px-4 text-sm text-gray-700 transition-all duration-200 hover:bg-gray-50 hover:shadow-sm">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-700">
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
                           class="flex h-11 items-center rounded-full px-4 text-sm text-gray-700 transition hover:bg-gray-100">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}"
                           class="flex h-11 items-center rounded-full bg-gradient-to-r from-red-500 to-red-600 px-5 text-sm font-semibold text-white transition hover:shadow-md">
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
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100 lg:hidden">
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
         class="max-h-[calc(100vh-5rem)] overflow-y-auto border-t border-gray-200 bg-white lg:hidden">

        <div class="space-y-1 px-4 py-4">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   @if($link['active']) aria-current="page" @endif
                   @class([
                       'flex items-center gap-3 rounded-xl px-4 py-3 text-base font-medium transition-colors',
                       'bg-red-50 text-red-700' => $link['active'],
                       'text-gray-700 hover:bg-gray-50' => ! $link['active'],
                   ])>
                    <span aria-hidden="true">{{ $link['icon'] }}</span>
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
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-700">
                        {{ strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        {{ __('Profile') }}
                    </a>

                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 rounded-xl px-4 py-3 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                            {{ __('nav.admin_panel') }}
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-base font-medium text-red-700 transition-colors hover:bg-red-50">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                       class="flex items-center justify-center rounded-xl border border-gray-200 px-4 py-3 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        {{ __('Log in') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 text-base font-semibold text-white transition hover:shadow-md">
                        {{ __('Register') }}
                    </a>
                </div>
            @endauth

            <div class="mt-3 border-t border-gray-100 px-2 pt-3">
                <x-language-switcher />
            </div>
        </div>
    </div>
</nav>
