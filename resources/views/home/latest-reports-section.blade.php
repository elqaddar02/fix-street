<!-- Recent Reports Section -->
<section id="recent-reports" class="bg-white py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-red-600">{{ __('Recent Activity') }}</p>
                <h2 class="mt-1.5 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('Latest Reports') }}</h2>
            </div>
            <a href="{{ route('reports.index') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                {{ __('View All Reports') }}
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>

        @if($latestReports->isNotEmpty())
            <div x-data="{
                currentIndex: 0,
                get totalSlides() { return Math.ceil({{ $latestReports->count() }} / 3); },
                next() { if (this.currentIndex < this.totalSlides - 1) this.currentIndex++; },
                prev() { if (this.currentIndex > 0) this.currentIndex--; },
                goTo(index) { this.currentIndex = index; }
            }" class="relative md:overflow-hidden">
                {{-- Phones: a sideways swipe row (native scroll-snap). Desktop: a 3-up slider. --}}
                <div class="flex transition-transform duration-300 ease-in-out max-md:-mx-4 max-md:snap-x max-md:snap-mandatory max-md:scroll-px-4 max-md:gap-3 max-md:overflow-x-auto max-md:px-4 max-md:pb-2 max-md:[scrollbar-width:none]" :style="`transform: translateX(${document.dir === 'rtl' ? '' : '-'}${currentIndex * 100}%)`">
                    @foreach($latestReports->chunk(3) as $chunk)
                        {{-- A little padding so card shadows aren't clipped by the slider. --}}
                        <div class="max-md:contents md:grid md:w-full md:flex-none md:grid-cols-3 md:gap-5 md:p-1">
                            @foreach($chunk as $report)
                                @include('home.report-card', ['report' => $report, 'cardClass' => 'max-md:w-[80%] max-md:shrink-0 max-md:snap-start'])
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Dots -->
                <div class="mt-6 hidden justify-center gap-2 md:flex">
                    <template x-for="(slide, index) in Array.from({length: totalSlides})" :key="index">
                        <button type="button" @click="goTo(index)" class="h-2 rounded-full transition-all" :class="index === currentIndex ? 'w-6 bg-red-600' : 'w-2 bg-gray-300 hover:bg-gray-400'" :aria-label="`${index + 1} / ${totalSlides}`"></button>
                    </template>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center">
                <p class="text-base font-semibold text-gray-900">{{ __('No reports yet') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Be the first to report an issue in your area.') }}</p>
                <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="mt-5 inline-flex items-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                    {{ __('Create First Report') }}
                </a>
            </div>
        @endif
    </div>
</section>
