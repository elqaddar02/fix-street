@php($title = 'Madinova - Official City Street Maintenance Portal')

<x-app-layout>
    <style>
        .hero-gradient { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .stat-gradient { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
        .gradient-text { background: linear-gradient(135deg, #dc2626, #991b1b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .pulse-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .report-card-image { aspect-ratio: 16/9; object-fit: cover; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .float-animation { animation: float 3s ease-in-out infinite; }
    </style>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div>
                    <div class="mb-6 flex items-center gap-3">
                        <div class="w-2 h-2 bg-yellow-300 rounded-full pulse-dot"></div>
                        <span class="text-yellow-100 text-sm font-bold uppercase tracking-widest">Improving Your City</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black mb-6 leading-tight tracking-tight">
                        Report Streets,<br><span class="text-yellow-300">Get Them Fixed</span>
                    </h1>
                    <p class="text-lg text-gray-100 mb-10 leading-relaxed max-w-xl">
                        Help us maintain safe, clean streets. Report potholes, broken lights, damaged infrastructure, and more. Your reports empower city officials to act fast.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-red-600 font-bold rounded-xl hover:bg-yellow-100 transition-all shadow-xl hover:shadow-2xl hover:scale-105">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Report a Problem
                        </a>
                        <a href="#recent-reports" class="inline-flex items-center justify-center px-8 py-4 bg-white/20 text-white font-bold rounded-xl hover:bg-white/30 transition-all border-2 border-white/60 hover:border-white">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            View Reports
                        </a>
                    </div>
                </div>

                <!-- Hero Illustration -->
                <div class="hidden md:block relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-3xl blur-2xl"></div>
                    <div class="float-animation">
                        <svg class="w-full h-auto" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.8">
                                <circle cx="200" cy="200" r="180" stroke="white" stroke-width="1.5" opacity="0.2"/>
                                <circle cx="200" cy="200" r="140" stroke="white" stroke-width="1.5" opacity="0.15"/>
                                <circle cx="200" cy="200" r="100" fill="white" opacity="0.05"/>
                                <rect x="140" y="140" width="120" height="120" fill="white" opacity="0.1" rx="20"/>
                                <path d="M100 200H300" stroke="white" stroke-width="1.5" opacity="0.15"/>
                                <path d="M200 100V300" stroke="white" stroke-width="1.5" opacity="0.15"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-6 mt-16 pt-8 border-t border-white/20">
                <div class="text-center">
                    <div class="text-3xl font-black text-yellow-300 mb-2">{{ \App\Models\Report::count() }}</div>
                    <p class="text-sm text-white/80">Total Reports</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-yellow-300 mb-2">{{ \App\Models\Report::where('status', 'RESOLVED')->count() }}</div>
                    <p class="text-sm text-white/80">Resolved Issues</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-yellow-300 mb-2">{{ \App\Models\City::count() }}</div>
                    <p class="text-sm text-white/80">Cities Covered</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ad Banner -->
    <section class="max-w-7xl mx-auto px-6 py-8">
        <x-ad-banner type="horizontal" />
    </section>

    <!-- Categories Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block text-red-600 font-bold text-sm uppercase tracking-wider mb-3">Categories</span>
                <h2 class="text-4xl font-black text-gray-900 mb-4">Report What You See</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Choose from these common street maintenance issues</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-red-200">
                    <div class="w-16 h-16 bg-red-100 flex items-center justify-center mb-6 rounded-xl">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Road Damage</h3>
                    <p class="text-gray-600 leading-relaxed">Potholes, cracks, uneven pavement, and other surface damage that poses safety risks.</p>
                </div>

                <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-yellow-200">
                    <div class="w-16 h-16 bg-yellow-100 flex items-center justify-center mb-6 rounded-xl">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Street Lighting</h3>
                    <p class="text-gray-600 leading-relaxed">Broken, malfunctioning, or missing street lights affecting pedestrian and driver safety.</p>
                </div>

                <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-blue-200">
                    <div class="w-16 h-16 bg-blue-100 flex items-center justify-center mb-6 rounded-xl">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Infrastructure</h3>
                    <p class="text-gray-600 leading-relaxed">Damaged signs, barriers, sidewalks, curbs, drainage issues, and other street infrastructure.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Reports Section -->
    <section id="recent-reports" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between mb-16">
                <div>
                    <span class="inline-block text-red-600 font-bold text-sm uppercase tracking-wider mb-3">Recent Activity</span>
                    <h2 class="text-4xl font-black text-gray-900">Latest Reports</h2>
                </div>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all">
                    View All Reports
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>

            @if($latestReports->isNotEmpty())
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($latestReports as $report)
                        <article class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-lg hover:shadow-2xl">
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
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">
                                        {{ $report->category->name ?? 'Unknown' }}
                                    </span>
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

                                <a href="{{ route('reports.show', $report) }}" class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all text-sm">
                                    View Details
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-600 font-semibold mb-4">No reports yet</p>
                    <p class="text-gray-500 mb-6">Be the first to report an issue in your area.</p>
                    <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all">
                        Create First Report
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block text-red-600 font-bold text-sm uppercase tracking-wider mb-3">Process</span>
                <h2 class="text-4xl font-black text-gray-900 mb-4">How It Works </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Simple steps to report and track street issues</p>
            </div>

            <div class="flex justify-center items-center">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-4xl">
                    <div class="text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-6 shadow-lg">1</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Take a Photo</h3>
                        <p class="text-gray-600">Capture a clear image of the street issue you found.</p>
                    </div>
                    <div class="text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-orange-600 text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-6 shadow-lg">2</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Describe Issue</h3>
                        <p class="text-gray-600">Fill in category, location, and detailed description.</p>
                    </div>
                    <div class="text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-green-600 text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-6 shadow-lg">3</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Get Fixed</h3>
                        <p class="text-gray-600">City team reviews and schedules maintenance action.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-black text-gray-900 mb-6">Make a Difference Today</h2>
            <p class="text-xl text-gray-600 mb-10">Every report helps improve our city. Be part of the solution.</p>
            <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center justify-center px-10 py-5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-xl hover:shadow-2xl text-lg">
                Start Reporting Now
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </section>
</x-app-layout>