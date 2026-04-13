<!-- Categories Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block text-red-600 font-bold text-sm uppercase tracking-wider mb-3">{{ __('Categories') }}</span>
            <h2 class="text-4xl font-black text-gray-900 mb-4">{{ __('Report What You See') }}</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ __('Choose from these common street maintenance issues') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-red-200">
                <div class="w-16 h-16 bg-red-100 flex items-center justify-center mb-6 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('Road Damage') }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ __('Potholes, cracks, uneven pavement, and other surface damage that poses safety risks.') }}</p>
            </div>

            <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-yellow-200">
                <div class="w-16 h-16 bg-yellow-100 flex items-center justify-center mb-6 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('Street Lighting') }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ __('Broken, malfunctioning, or missing street lights affecting pedestrian and driver safety.') }}</p>
            </div>

            <div class="card-hover bg-white rounded-2xl p-8 border-2 border-transparent hover:border-blue-200">
                <div class="w-16 h-16 bg-blue-100 flex items-center justify-center mb-6 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ __('Infrastructure') }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ __('Damaged signs, barriers, sidewalks, curbs, drainage issues, and other street infrastructure.') }}</p>
            </div>
        </div>
    </div>
</section>
