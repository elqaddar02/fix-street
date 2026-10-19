@php
    // Phone navigation: the main destinations one thumb-tap away, with
    // "New report" — the action the whole site exists for — in the middle.
    $icons = [
        'home' => 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'list' => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
        'grid' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
        'user' => 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        'help' => 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
        'login' => 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75',
    ];

    $tabs = auth()->user()?->is_admin
        // Admins moderate rather than report: no "+", and their space is the admin panel.
        ? [
            ['route' => 'home', 'label' => __('nav.home'), 'icon' => 'home', 'active' => request()->routeIs('home')],
            ['route' => 'reports.index', 'label' => __('nav.all_reports'), 'icon' => 'list', 'active' => request()->routeIs('reports.index', 'reports.show')],
            ['route' => 'admin.dashboard', 'label' => __('nav.admin_panel'), 'icon' => 'grid', 'active' => false],
            ['route' => 'profile.edit', 'label' => __('nav.profile_tab'), 'icon' => 'user', 'active' => request()->routeIs('profile.*')],
        ]
        : (auth()->check()
        ? [
            ['route' => 'home', 'label' => __('nav.home'), 'icon' => 'home', 'active' => request()->routeIs('home')],
            ['route' => 'reports.index', 'label' => __('nav.all_reports'), 'icon' => 'list', 'active' => request()->routeIs('reports.index', 'reports.show')],
            'new',
            ['route' => 'dashboard', 'label' => __('nav.my_reports'), 'icon' => 'grid', 'active' => request()->routeIs('dashboard', 'reports.edit')],
            ['route' => 'profile.edit', 'label' => __('nav.profile_tab'), 'icon' => 'user', 'active' => request()->routeIs('profile.*')],
        ]
        : [
            ['route' => 'home', 'label' => __('nav.home'), 'icon' => 'home', 'active' => request()->routeIs('home')],
            ['route' => 'reports.index', 'label' => __('nav.all_reports'), 'icon' => 'list', 'active' => request()->routeIs('reports.index', 'reports.show')],
            'new',
            ['route' => 'help', 'label' => __('nav.help'), 'icon' => 'help', 'active' => request()->routeIs('help')],
            ['route' => 'login', 'label' => __('Log in'), 'icon' => 'login', 'active' => request()->routeIs('login')],
        ]);
@endphp

<nav aria-label="{{ __('nav.main') }}"
     class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur lg:hidden">
    <ul @class(["mx-auto grid h-16 max-w-md", "grid-cols-5" => count($tabs) === 5, "grid-cols-4" => count($tabs) === 4])>
        @foreach($tabs as $tab)
            <li class="flex">
                @if($tab === 'new')
                    {{-- Guests are sent through login by the route's auth middleware. --}}
                    <a href="{{ route('reports.create') }}" @if(request()->routeIs('reports.create')) aria-current="page" @endif
                       class="mx-auto -mt-4 flex h-14 w-14 items-center justify-center self-start rounded-full bg-red-600 text-white shadow-lg shadow-red-600/30 ring-4 ring-white transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-red-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span class="sr-only">{{ __('dashboard.new_report') }}</span>
                    </a>
                @else
                    <a href="{{ route($tab['route']) }}" @if($tab['active']) aria-current="page" @endif
                       @class([
                           'flex w-full flex-col items-center justify-center gap-1 text-[11px] font-medium leading-tight transition-colors',
                           'text-red-700' => $tab['active'],
                           'text-gray-500 hover:text-gray-900' => ! $tab['active'],
                       ])>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="{{ $tab['active'] ? '2' : '1.6' }}" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$tab['icon']] }}"/></svg>
                        <span class="max-w-full truncate px-1">{{ $tab['label'] }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
