<footer class="bg-slate-950 text-slate-100 border-t border-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 pb-10 border-b border-slate-900">
            
            <div class="space-y-3 max-w-md">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-10 w-auto fill-current text-indigo-500" />
                    <span class="text-xl font-bold tracking-tight text-white">
                        {{ config('app.name', 'Madinova') }}
                    </span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    {{ __('footer.tagline') }}
                </p>
            </div>

            
        </div>

        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
            <p class="text-xs text-slate-500 tracking-wide">
                &copy; 2026 Madinova. {{ __('footer.all_rights_reserved') }}
            </p>
            <div class="h-1.5 w-1.5 rounded-full bg-indigo-500/50 hidden sm:block"></div>
            <p class="text-xs text-slate-500 font-medium">
                {{ __('footer.tagline') }}
            </p>
        </div>
    </div>
</footer>