<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tightest uppercase">
                       {{ __('All Reports') }}
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Live Feed Active') }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-6 bg-white border border-slate-200 px-6 py-3 rounded-2xl shadow-sm">
                <div class="text-center border-r border-slate-100 pr-6">
                    <p class="text-[10px] font-black uppercase text-slate-400 leading-none mb-1">{{ __('Total') }}</p>
                    <p class="text-lg font-black text-slate-900 leading-none">{{ $reports->total() }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase text-slate-400 leading-none mb-1">{{ __('New') }}</p>
                    <p class="text-lg font-black text-red-600 leading-none">{{ $reports->where('created_at', '>=', now()->subDay())->count() }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#F8FAFC] min-h-screen relative">
        <div class="absolute inset-0 opacity-[0.015] pointer-events-none" style="background-image: radial-gradient(#0f172a 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-4 gap-10">
                
                <aside class="lg:col-span-1">
                    <div class="sticky top-10 space-y-4">
                        <div class="bg-white border border-slate-200 rounded-[2rem] p-8 shadow-xl shadow-slate-200/40">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">{{ __('Filter Panel') }}</h3>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>

                            <form method="GET" action="{{ route('reports.index') }}" class="space-y-6">
                                <div class="group">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 group-focus-within:text-red-600 transition">{{ __('Category') }}</label>
                                    <select name="category" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-700 transition focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-50/50 appearance-none">
                                        <option value="">{{ __('All Domains') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 group-focus-within:text-red-600 transition">{{ __('City') }}</label>
                                    <select name="city" id="city-select" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-700 transition focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-50/50 appearance-none">
                                        <option value="">{{ __('All Cities') }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @selected($selectedCity == $city->id)>{{ $city->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 group-focus-within:text-red-600 transition">{{ __('District') }}</label>
                                    <select name="district" id="district-select" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-700 transition focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-50/50 appearance-none">
                                        <option value="">{{ __('All Districts') }}</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" @selected($selectedDistrict == $district->id)>{{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="group">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 group-focus-within:text-red-600 transition">{{ __('Timeline') }}</label>
                                    <select name="date" class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-700 transition focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-50/50 appearance-none">
                                        <option value="">{{ __('Full History') }}</option>
                                        <option value="today" @selected($selectedDate === 'today')>{{ __('Last 24 Hours') }}</option>
                                        <option value="week" @selected($selectedDate === 'week')>{{ __('This Week') }}</option>
                                        <option value="month" @selected($selectedDate === 'month')>{{ __('This Month') }}</option>
                                    </select>
                                </div>

                                <div class="pt-6 border-t border-slate-50 space-y-3">
                                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-4 text-xs font-black uppercase tracking-[0.2em] text-white transition hover:bg-red-600 shadow-lg shadow-slate-900/10 active:scale-95">
                                        {{ __('Update View') }}
                                    </button>
                                    <a href="{{ route('reports.index') }}" class="block w-full text-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-600 transition">
                                        {{ __('Clear Filters') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </aside>

                <section x-data="reportsView()" x-init="init()" class="lg:col-span-3 space-y-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">{{ __('Sort Selection') }}</h3>
                            <div class="h-px w-12 bg-slate-200"></div>
                            <select id="sort-select" class="border-none bg-transparent text-xs font-black uppercase tracking-widest text-red-600 focus:ring-0 cursor-pointer">
                                <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>{{ __('Latest First') }}</option>
                                <option value="mostLiked" {{ $sortBy === 'mostLiked' ? 'selected' : '' }}>{{ __('Most Urgent') }}</option>
                                <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('reports.create') }}" class="group inline-flex items-center gap-3 rounded-full bg-red-600 pl-6 pr-2 py-2 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-red-200 hover:bg-slate-900 transition-all">
                                {{ __('New Report') }}
                                <div class="bg-white/20 p-2 rounded-full group-hover:bg-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- List View -->
                    <div id="list-view" class="relative">
                        <div id="loading-indicator" class="absolute inset-0 z-20 bg-white/80 backdrop-blur-sm flex items-center justify-center hidden">
                            <div class="flex items-center gap-3 text-slate-700">
                                <div class="h-4 w-4 rounded-full animate-bounce bg-red-600"></div>
                                <span class="font-semibold">Loading reports…</span>
                            </div>
                        </div>

                        <div id="results-container">
                            @include('reports.partials.results')
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        // Register Alpine component
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportsView', () => ({
                loading: false,
                sortBy: '{{ $sortBy }}',
                init() {
                    this.bindFormEvents();
                    this.bindSortSelect();
                    this.bindPagination();
                },
                bindFormEvents() {
                    const form = document.querySelector('form[action="{{ route("reports.index") }}"]');
                    if (!form) return;

                    form.addEventListener('submit', e => {
                        e.preventDefault();
                        this.fetchReports(form);
                    });

                    form.querySelectorAll('select').forEach(select => {
                        select.addEventListener('change', () => this.fetchReports(form));
                    });
                },
                bindSortSelect() {
                    const sortSelect = document.getElementById('sort-select');
                    if (sortSelect) {
                        sortSelect.addEventListener('change', () => {
                            this.sortBy = sortSelect.value;
                            const form = document.querySelector('form[action="{{ route("reports.index") }}"]');
                            this.fetchReports(form);
                        });
                    }
                },
                bindPagination() {
                    const container = document.getElementById('results-container');
                    if (container) {
                        container.addEventListener('click', e => {
                            const link = e.target.closest('a[href*="?page="]');
                            if (link) {
                                e.preventDefault();
                                const url = new URL(link.href);
                                this.fetchReports(null, url);
                            }
                        });
                    }
                },
                async fetchReports(form = null, url = null) {
                    const loadingEl = document.getElementById('loading-indicator');
                    const container = document.getElementById('results-container');
                    if (loadingEl) loadingEl.classList.remove('hidden');
                    this.loading = true;
                    let fetchUrl;

                    if (url) {
                        fetchUrl = url.toString();
                    } else if (form) {
                        const params = new URLSearchParams(new FormData(form));
                        params.set('sort', this.sortBy);
                        fetchUrl = `{{ route('reports.index') }}?${params.toString()}`;
                    } else {
                        if (loadingEl) loadingEl.classList.add('hidden');
                        this.loading = false;
                        return;
                    }

                    try {
                        const response = await fetch(fetchUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) throw new Error('Failed to fetch');

                        const data = await response.json();
                        if (data.success && data.html) {
                            container.innerHTML = data.html;
                            window.history.pushState({}, '', fetchUrl);
                            this.bindPagination();
                        }
                    } catch (err) {
                        console.error('Error fetching reports:', err);
                        container.innerHTML = '<p class="col-span-2 text-center text-red-600 font-semibold">Error loading reports. Please try again.</p>';
                    } finally {
                        if (loadingEl) loadingEl.classList.add('hidden');
                        this.loading = false;
                    }
                }
            }));
        });

        // District filtering on reports index - districts are global, no city-based filtering needed
        
    </script>
</x-app-layout>