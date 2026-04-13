<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-black text-gray-900 mb-6">{{ __('Make a Difference Today') }}</h2>
        <p class="text-xl text-gray-600 mb-10">{{ __('Every report helps improve our city. Be part of the solution.') }}</p>
        <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="inline-flex items-center justify-center px-10 py-5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-xl hover:shadow-2xl text-lg">
            {{ __('Start Reporting Now') }}
            <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
    </div>
</section>
