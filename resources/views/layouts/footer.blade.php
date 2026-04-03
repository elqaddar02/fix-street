<footer class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-slate-100 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
            <!-- Logo and Branding -->
            <div class="flex flex-col items-start space-y-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <x-application-logo class="h-32 w-auto fill-current text-indigo-400 group-hover:text-indigo-200 transition-colors" />
                   
                </a>
                <p class="text-slate-300 leading-relaxed max-w-xs">{{ __('footer.tagline') }}</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ __('footer.navigation') }}</h3>
                <ul class="space-y-3">
                    <li><a href="{{ url('/') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.home') }}</a></li>
                    <li><a href="{{ route('about') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.about') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.contact') }}</a></li>
                    @if(auth()->check())
                        <li><a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.dashboard') }}</a></li>
                    @endif
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ __('footer.resources') }}</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('privacy') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.privacy_policy') }}</a></li>
                    <li><a href="#" onclick="event.preventDefault(); window.openTermsModal();" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.terms_of_service') }}</a></li>
                    <li><a href="{{ route('reports.index') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.view_reports') }}</a></li>
                </ul>
            </div>

            <!-- Account Links -->
            <div>
                <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ __('footer.account') }}</h3>
                <ul class="space-y-3">
                    @auth
                        <li><a href="{{ route('profile.edit') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('footer.my_profile') }}</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm text-left">{{ __('Logout') }}</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('Login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="text-slate-300 hover:text-indigo-300 transition-colors text-sm">{{ __('Register') }}</a></li>
                    @endauth
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="border-t border-slate-800 pt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-slate-400 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'Fix Street') }}. {{ __('footer.all_rights_reserved') }}
                <span class="text-slate-500">{{ __('footer.built_with_care') }}</span>
            </p>

            <div class="flex items-center gap-4">
                <x-language-switcher />
                <span class="text-slate-400 text-sm">{{ __('language.change') }}</span>

                <div class="flex items-center space-x-5">
                    <a href="#" class="text-slate-400 hover:text-indigo-300 transition-colors" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-300 transition-colors" aria-label="Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7a10.6 10.6 0 01-9.56 5.12A9.5 9.5 0 0023 15z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-300 transition-colors" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 11.37A4 4 0 1112.63 8A4 4 0 0116 11.37z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
