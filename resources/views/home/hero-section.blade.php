<!-- Hero Section -->
<section class="hero-gradient text-white py-24 relative overflow-hidden fade-in">
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
                    <span class="text-yellow-100 text-sm font-bold uppercase tracking-widest">{{ __('Improving Your City') }}</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-black mb-6 leading-[1.5] tracking-tight" aria-label="Report Streets, Get Them Fixed">
                    <span id="line1" data-text="{{ __('Report Streets,') }}" class="inline-block"></span><br>
                    <span id="line2" data-text="{{ __('Get Them Fixed') }}" class="text-yellow-300 inline-block"></span>
                </h1>
                <p class="text-lg text-gray-100 mb-10 leading-relaxed max-w-xl">
                    {{ __('Help us maintain safe, clean streets. Report potholes, broken lights, damaged infrastructure, and more. Your reports empower city officials to act fast.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-red-600 font-bold rounded-xl hover:bg-yellow-100 transition-all shadow-xl hover:shadow-2xl hover:scale-105">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Report a Problem') }}
                    </a>
                    <a href="#recent-reports" class="inline-flex items-center justify-center px-8 py-4 bg-white/20 text-white font-bold rounded-xl hover:bg-white/30 transition-all border-2 border-white/60 hover:border-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        {{ __('View Reports') }}
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
        @php
            $totalReports = \App\Models\Report::count();
            $resolvedCount = \App\Models\Report::where('status', 'RESOLVED')->count();
            $resolvedPercentage = $totalReports > 0 ? ($resolvedCount / $totalReports) * 100 : 0;
            $resolvedPercentage = min($resolvedPercentage + 20, 100); // Add 20% but max 100%
            $citiesCount = \App\Models\City::count();
        @endphp
        <div class="grid grid-cols-3 gap-6 mt-16 pt-8 border-t border-white/20">
            <!-- Total Reports Stat -->
            <div class="text-center">
                <div class="stat-counter text-4xl font-black text-yellow-300 mb-3" data-target="{{ $totalReports }}">0</div>
                <p class="text-sm text-white/80 font-semibold uppercase tracking-wider">{{ __('Total Reports') }}</p>
            </div>
            
            <!-- Resolved Issues Stat with Percentage -->
            <div class="text-center">
                <div class="mb-3">
                    <div class="relative w-24 h-24 mx-auto mb-2">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <!-- Background circle -->
                            <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="8"/>
                            <!-- Progress circle -->
                            <circle class="stat-progress-circle" cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-dasharray="282.7" stroke-dashoffset="282.7" style="color: #FBBF24; transition: stroke-dashoffset 2s ease-out;"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="stat-percentage text-2xl font-black text-yellow-300">0%</span>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-white/80 font-semibold uppercase tracking-wider">{{ __('Resolved Issues') }}</p>
            </div>
            
            <!-- Cities Covered Stat -->
            <div class="text-center">
                <div class="stat-counter text-4xl font-black text-yellow-300 mb-3" data-target="{{ $citiesCount }}+">0</div>
                <p class="text-sm text-white/80 font-semibold uppercase tracking-wider">{{ __('Cities Covered') }}</p>
            </div>
        </div>
        
        <!-- Store percentage value for JavaScript -->
        <div class="hidden" id="resolved-percentage" data-percentage="{{ round($resolvedPercentage) }}"></div>
    </div>
</section>
