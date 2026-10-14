<footer class="border-t-4 border-[#1b2a41] bg-[#0f1b2d] text-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-8 border-b border-slate-700">
            <div class="space-y-3 max-w-md">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-10 w-auto fill-current text-slate-300" />
                    <span class="font-serif text-xl font-semibold tracking-tight text-white">
                        {{ config('app.name', 'Madinup') }}
                    </span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">
                    {{ __('footer.tagline') }}
                </p>
            </div>
        </div>

        <div class="pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-center sm:text-start">
            <p class="text-xs text-slate-400">
                &copy; 2026 Madinup. {{ __('footer.all_rights_reserved') }}
            </p>
            <p class="text-xs text-slate-400">
                {{ __('footer.independent') }}
            </p>
        </div>
    </div>
</footer>
