<nav
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
    :class="scrolled 
        ? 'bg-white/85 backdrop-blur-xl shadow-md h-20' 
        : 'bg-white h-24'"
    class="fixed top-0 left-0 w-full z-50 border-b border-gray-200 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full">

            <!-- LOGO -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">

                    <div
                        x-bind:class="scrolled ? 'h-14 scale-105' : 'h-24 scale-110'"
                        class="flex items-center transition-all duration-300 origin-left">
                        <x-application-logo class="w-auto h-full" />
                    </div>

                </a>
            </div>

            <!-- CENTER NAV -->
            <div class="hidden sm:flex flex-1 justify-center">

                <div class="flex items-center space-x-2 bg-white/60 backdrop-blur-md border border-gray-200 rounded-full px-2 py-1 shadow-sm">

                    <x-nav-link
                        :href="route('reports.index')"
                        :active="request()->routeIs('reports.index')"
                        class="flex items-center gap-2 px-5 h-11 rounded-full text-sm font-medium text-gray-700
                        transition-all duration-300 hover:bg-white hover:shadow-md hover:scale-105">
                        📄 {{ __('nav.all_reports') }}
                    </x-nav-link>

                    <x-nav-link
                        :href="route('help')"
                        :active="request()->routeIs('help')"
                        class="flex items-center gap-2 px-5 h-11 rounded-full text-sm font-medium text-gray-700
                        transition-all duration-300 hover:bg-white hover:shadow-md hover:scale-105">
                        ❓ {{ __('nav.help') }}
                    </x-nav-link>

                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden sm:flex items-center gap-3">

                <!-- LANGUAGE SWITCHER -->
                <x-language-switcher />

                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-5 h-11 rounded-full
                            bg-white/70 border border-gray-200 text-sm text-gray-700
                            hover:shadow-md hover:scale-[1.03]
                            transition-all duration-300">

                            <span class="font-medium">{{ Auth::user()->name }}</span>

                            <svg class="w-4 h-4 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>

                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                @else
                <div class="flex items-center gap-2">

                    <a href="{{ route('login') }}"
                        class="h-11 flex items-center px-4 rounded-full text-sm text-gray-700 hover:bg-gray-100 transition">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                        class="h-11 flex items-center px-5 rounded-full text-sm font-semibold text-white
                        bg-gradient-to-r from-red-500 to-red-600 hover:shadow-md hover:scale-105 transition">
                        Register
                    </a>

                </div>
                @endauth

            </div>

            <!-- MOBILE -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-full hover:bg-gray-100 transition">
                    ☰
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div :class="open ? 'block' : 'hidden'" class="sm:hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-3 space-y-2">

            <x-responsive-nav-link
                :href="route('reports.index')"
                :active="request()->routeIs('reports.index')">
                {{ __('nav.all_reports') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('help')"
                :active="request()->routeIs('help')">
                {{ __('nav.help') }}
            </x-responsive-nav-link>

        </div>
    </div>

</nav>