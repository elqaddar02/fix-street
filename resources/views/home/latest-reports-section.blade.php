<!-- Recent Reports Section -->
<section id="recent-reports" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-16">
            <div>
                <span class="inline-block text-red-600 font-bold text-sm uppercase tracking-wider mb-3">{{ __('Recent Activity') }}</span>
                <h2 class="text-4xl font-black text-gray-900">{{ __('Latest Reports') }}</h2>
            </div>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all">
                {{ __('View All Reports') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>

        @if($latestReports->isNotEmpty())
            <div x-data="{
                currentIndex: 0,
                get totalSlides() { return Math.ceil({{ $latestReports->count() }} / 3); },
                next() { if (this.currentIndex < this.totalSlides - 1) this.currentIndex++; },
                prev() { if (this.currentIndex > 0) this.currentIndex--; },
                goTo(index) { this.currentIndex = index; }
            }" class="relative overflow-hidden">
                <div class="flex transition-transform duration-300 ease-in-out" :style="`transform: translateX(-${currentIndex * 100}%)`">
                    @foreach($latestReports->chunk(3) as $chunk)
                        <div class="flex-none w-full grid grid-cols-1 md:grid-cols-3 gap-8 px-4">
                            @foreach($chunk as $report)
                                <a href="{{ route('reports.show', $report) }}" class="block card-hover bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-lg hover:shadow-2xl">
                                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                                        @if($report->image && file_exists(storage_path('app/public/' . $report->image)))
                                            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="report-card-image w-full h-full">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-orange-50">
                                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 right-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase"
                                                @switch($report->status)
                                                    @case('OPEN')
                                                        style="background-color: #fee2e2; color: #991b1b;"
                                                    @break
                                                    @case('IN_PROGRESS')
                                                        style="background-color: #fed7aa; color: #92400e;"
                                                    @break
                                                    @case('RESOLVED')
                                                        style="background-color: #dcfce7; color: #166534;"
                                                    @break
                                                @endswitch
                                            >{{ $report->status }}</span>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex flex-wrap gap-2">
                                                <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                                                    {{ $report->category->display_name ?? 'Unknown' }}
                                                </span>
                                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                                    {{ $report->city->display_name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                            <time class="text-xs text-gray-500 font-semibold">{{ $report->created_at->format('M d, Y') }}</time>
                                        </div>

                                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $report->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $report->description }}</p>

                                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-red-200 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-bold text-red-700">{{ substr($report->user->name ?? 'A', 0, 1) }}</span>
                                                </div>
                                                <span class="text-xs text-gray-700 font-semibold">{{ $report->user->name ?? 'Anonymous' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 text-gray-500">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10a2 2 0 104 0 2 2 0 00-4 0zM14 10a2 2 0 104 0 2 2 0 00-4 0z"/></svg>
                                                <span class="text-xs font-semibold">{{ $report->comments->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Dots -->
                <div class="flex justify-center mt-8 space-x-2">
                    <template x-for="(slide, index) in Array.from({length: totalSlides})" :key="index">
                        <button @click="goTo(index)" class="w-3 h-3 rounded-full transition-all" :class="index === currentIndex ? 'bg-red-600' : 'bg-gray-300'"></button>
                    </template>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600 font-semibold mb-4">{{ __('No reports yet') }}</p>
                <p class="text-gray-500 mb-6">{{ __('Be the first to report an issue in your area.') }}</p>
                <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all">
                    {{ __('Create First Report') }}
                </a>
            </div>
        @endif
    </div>
</section>
