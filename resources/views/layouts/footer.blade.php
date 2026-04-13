<footer class="bg-slate-950 text-slate-100 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-16 w-auto fill-current text-indigo-400" />
                <span class="text-lg font-bold tracking-tight text-white">
                    {{ config('app.name', 'Madinova') }}
                </span>
            </div>

            <nav>
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm font-medium text-slate-400">
                    <li><a href="{{ url('/') }}" class="hover:text-indigo-300 transition-colors">{{ __('footer.home') }}</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-indigo-300 transition-colors">{{ __('footer.help') }}</a></li>
                    <li><a href="{{ route('reports.index') }}" class="hover:text-indigo-300 transition-colors">{{ __('footer.view_reports') }}</a></li>
                    @guest
                        <li><a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">{{ __('Login') }}</a></li>
                    @endguest
                </ul>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="#" class="text-slate-500 hover:text-indigo-400 transition-colors" aria-label="Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="text-slate-500 hover:text-indigo-400 transition-colors" aria-label="Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7a10.6 10.6 0 01-9.56 5.12A9.5 9.5 0 0023 15z"/></svg>
                </a>
            </div>
        </div>

        <div class="border-t border-slate-900 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <x-language-switcher />
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}.
                </p>
            </div>
            
            <p class="text-xs text-slate-600 italic">
                {{ __('footer.tagline') }}
            </p>
        </div>
    </div>
</footer>