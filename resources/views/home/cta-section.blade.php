<!-- CTA Section -->
<section class="bg-white py-12 sm:py-16">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('Make a Difference Today') }}</h2>
        <p class="mt-2 text-sm text-gray-500 sm:text-base">{{ __('Every report helps improve our city. Be part of the solution.') }}</p>
        <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
            {{ __('Start Reporting Now') }}
            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
    </div>
</section>
