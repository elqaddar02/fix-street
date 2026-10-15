@php
    $title = __('nav.all_reports') . ' - Madinup';
    $field = 'mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
    $label = 'block text-sm font-medium text-gray-700';
    $activeFilters = collect([$selectedCategory, $selectedCity, $selectedDistrict, $selectedQuartier, $selectedDate])->filter(fn ($v) => filled($v))->count();
    $filterIcon = 'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75';
@endphp

<x-app-layout>
    {{-- `sheet` drives the phone filter sheet; `active` counts the filters in use. --}}
    <div x-data="{ sheet: false, active: {{ $activeFilters }} }" @keydown.escape.window="sheet = false"
         class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <header class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between lg:mb-6">
            <div class="min-w-0">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('nav.all_reports') }}</h1>
                <p id="reports-summary" class="mt-1 text-sm text-gray-500" aria-live="polite">
                    @include('reports.partials.summary')
                </p>
            </div>
            {{-- Phones reach "New report" from the tab bar. --}}
            <a href="{{ route('reports.create') }}" class="hidden shrink-0 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 lg:inline-flex">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                {{ __('dashboard.new_report') }}
            </a>
        </header>

        <div class="lg:grid lg:grid-cols-4 lg:gap-6">
            {{-- Phones: a dimmed backdrop behind the filter sheet. --}}
            <div x-show="sheet" x-cloak x-transition.opacity @click="sheet = false" class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden" aria-hidden="true"></div>

            {{--
                Desktop: a sticky sidebar. Phones: the same panel becomes a
                bottom sheet, so the results are visible as soon as the page opens.
            --}}
            <aside id="filters-panel" class="lg:col-span-1"
                   :role="sheet ? 'dialog' : null" :aria-modal="sheet ? 'true' : null" aria-labelledby="filters-title">
                <div :class="sheet && 'max-lg:!translate-y-0'"
                     class="fixed inset-x-0 bottom-0 z-50 max-h-[85vh] overflow-y-auto rounded-t-xl bg-white px-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] pt-3 shadow-[0_-8px_30px_rgba(17,24,39,0.12)] transition-transform duration-200 max-lg:translate-y-full lg:static lg:z-auto lg:max-h-none lg:overflow-visible lg:rounded-xl lg:border lg:border-gray-200 lg:p-5 lg:shadow-[0_3px_12px_rgba(17,24,39,0.04)] lg:sticky lg:top-24">
                    <div class="mx-auto mb-3 h-1 w-10 rounded-full bg-gray-300 lg:hidden" aria-hidden="true"></div>
                    <div class="flex items-center justify-between gap-2">
                        <h2 id="filters-title" class="flex items-center gap-2 text-base font-semibold text-gray-900">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $filterIcon }}"/></svg>
                            {{ __('reports.filters') }}
                        </h2>
                        <button type="button" @click="sheet = false" class="-me-2 inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 lg:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            <span class="sr-only">{{ __('Close') }}</span>
                        </button>
                    </div>

                    <form id="filters-form" method="GET" action="{{ route('reports.index') }}" class="mt-4 space-y-4"
                          @change="active = [...$el.querySelectorAll('select')].filter(s => s.value).length">
                        <div>
                            <label class="{{ $label }}" for="filter-category">{{ __('Category') }}</label>
                            <select name="category" id="filter-category" class="{{ $field }}">
                                <option value="">{{ __('reports.all_categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $label }}" for="city-select">{{ __('City') }}</label>
                            <select name="city" id="city-select" class="{{ $field }}">
                                <option value="">{{ __('reports.all_cities') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" @selected($selectedCity == $city->id)>{{ $city->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $label }}" for="district-select">{{ __('District / Arrondissement') }}</label>
                            <select name="district" id="district-select" class="{{ $field }}">
                                <option value="">{{ __('reports.all_districts') }}</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" data-city-id="{{ $district->city_id }}" @selected($selectedDistrict == $district->id)>{{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $label }}" for="quartier-select">{{ __('Quartier') }}</label>
                            <select name="quartier" id="quartier-select" class="{{ $field }}">
                                <option value="">{{ __('reports.all_quartiers') }}</option>
                                @foreach ($quartiers as $quartier)
                                    <option value="{{ $quartier->id }}" data-district-id="{{ $quartier->district_id }}" @selected($selectedQuartier == $quartier->id)>{{ app()->getLocale() === 'ar' ? $quartier->name_ar : $quartier->name_fr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $label }}" for="filter-date">{{ __('reports.date') }}</label>
                            <select name="date" id="filter-date" class="{{ $field }}">
                                <option value="">{{ __('reports.any_time') }}</option>
                                <option value="today" @selected($selectedDate === 'today')>{{ __('reports.last_24h') }}</option>
                                <option value="week" @selected($selectedDate === 'week')>{{ __('reports.this_week') }}</option>
                                <option value="month" @selected($selectedDate === 'month')>{{ __('reports.this_month') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-3 border-t border-gray-100 pt-4">
                            <x-primary-button class="flex-1" @click="sheet = false">{{ __('reports.apply') }}</x-primary-button>
                            <a href="{{ route('reports.index') }}" class="text-sm font-medium text-gray-500 transition-colors hover:text-gray-900">{{ __('reports.clear') }}</a>
                        </div>
                    </form>
                </div>
            </aside>

            <section x-data="reportsView()" x-init="init()" class="space-y-4 lg:col-span-3">
                {{-- Phones: stays under the top bar while scrolling. --}}
                <div class="sticky top-16 z-20 -mx-4 flex items-center justify-between gap-2 border-b border-gray-200 bg-gray-50/95 px-4 py-2 backdrop-blur lg:static lg:mx-0 lg:justify-end lg:border-0 lg:bg-transparent lg:p-0 lg:backdrop-blur-none">
                    <button type="button" @click="sheet = true" aria-controls="filters-panel" :aria-expanded="sheet ? 'true' : 'false'"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm lg:hidden">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $filterIcon }}"/></svg>
                        {{ __('reports.filters') }}
                        <span x-show="active > 0" x-cloak x-text="active" class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white"></span>
                    </button>
                    <div class="flex items-center gap-2">
                        <label for="sort-select" class="hidden text-sm text-gray-500 sm:inline">{{ __('reports.sort_by') }}</label>
                        <select id="sort-select" aria-label="{{ __('reports.sort_by') }}" class="rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="latest" @selected($sortBy === 'latest')>{{ __('reports.sort.latest') }}</option>
                            <option value="mostLiked" @selected($sortBy === 'mostLiked')>{{ __('reports.sort.most_confirmed') }}</option>
                            <option value="oldest" @selected($sortBy === 'oldest')>{{ __('reports.sort.oldest') }}</option>
                        </select>
                    </div>
                </div>

                <div id="list-view" class="relative">
                    <div id="loading-indicator" class="absolute inset-0 z-10 flex hidden items-center justify-center rounded-xl bg-white/80 backdrop-blur-sm">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 animate-spin text-red-600" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span>{{ __('reports.loading') }}</span>
                        </div>
                    </div>

                    <div id="results-container">
                        @include('reports.partials.results')
                    </div>
                </div>
            </section>
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
                            if (data.summary) document.getElementById('reports-summary').innerHTML = data.summary;
                            window.history.pushState({}, '', fetchUrl);
                            this.bindPagination();
                        }
                    } catch (err) {
                        console.error('Error fetching reports:', err);
                        container.innerHTML = '<p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">' + @js(__('reports.load_error')) + '</p>';
                    } finally {
                        if (loadingEl) loadingEl.classList.add('hidden');
                        this.loading = false;
                    }
                }
            }));
        });

        // Dependent dropdowns for index page filters
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('city-select');
            const districtSelect = document.getElementById('district-select');
            const quartierSelect = document.getElementById('quartier-select');

            if (!citySelect || !districtSelect || !quartierSelect) return;

            const allDistrictOptions = Array.from(districtSelect.options);
            const allQuartierOptions = Array.from(quartierSelect.options);

            function updateDistricts() {
                const cityId = citySelect.value;
                const currentDistrictId = districtSelect.value;

                districtSelect.innerHTML = '';
                allDistrictOptions.forEach(option => {
                    if (!option.value || !cityId || option.dataset.cityId === cityId) {
                        districtSelect.appendChild(option.cloneNode(true));
                    }
                });

                if (currentDistrictId && Array.from(districtSelect.options).some(o => o.value === currentDistrictId)) {
                    districtSelect.value = currentDistrictId;
                } else {
                    districtSelect.value = '';
                }
                updateQuartiers();
            }

            function updateQuartiers() {
                const districtId = districtSelect.value;
                const currentQuartierId = quartierSelect.value;

                quartierSelect.innerHTML = '';
                allQuartierOptions.forEach(option => {
                    if (!option.value || !districtId || option.dataset.districtId === districtId) {
                        quartierSelect.appendChild(option.cloneNode(true));
                    }
                });

                if (currentQuartierId && Array.from(quartierSelect.options).some(o => o.value === currentQuartierId)) {
                    quartierSelect.value = currentQuartierId;
                } else {
                    quartierSelect.value = '';
                }
            }

            citySelect.addEventListener('change', updateDistricts);
            districtSelect.addEventListener('change', updateQuartiers);

            // Initialize on load if there are pre-selected values
            updateDistricts();
        });
    </script>
</x-app-layout>