<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex justify-between items-center h-16">
            <!-- Left: logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <x-application-logo class="block h-32 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <!-- Center: primary nav links -->
            <div class="hidden sm:flex flex-1 justify-center space-x-8">
                <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    {{ __('All Reports') }}
                </x-nav-link>
                <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                    {{ __('About') }}
                </x-nav-link>
                <x-nav-link :href="route('help')" :active="request()->routeIs('help')">
                    {{ __('Help') }}
                </x-nav-link>
                @auth
                    @if (auth()->user()->is_admin)
                        <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                            {{ __('Manage Reports') }}
                        </x-nav-link>
                    @endif
                @endauth
            </div>

            <!-- Right: language toggle and settings dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Language Toggle -->
                <div class="flex items-center space-x-2">
                    <a href="{{ url()->current() }}?lang=fr" class="px-2 py-1 text-sm rounded {{ app()->getLocale() === 'fr' ? 'bg-red-600 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                        FR
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ url()->current() }}?lang=en" class="px-2 py-1 text-sm rounded {{ app()->getLocale() === 'en' ? 'bg-red-600 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                        EN
                    </a>
                </div>

                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>
                                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Login</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                {{ __('All Reports') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                {{ __('About') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('help')" :active="request()->routeIs('help')">
                {{ __('Help') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
                @if (auth()->user()->is_admin)
                    <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                        {{ __('Manage Reports') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{ optional(Auth::user())->name ?? 'Guest' }}
                </div>
                <div class="font-medium text-sm text-gray-500">{{ optional(Auth::user())->email ?? 'Guest' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>