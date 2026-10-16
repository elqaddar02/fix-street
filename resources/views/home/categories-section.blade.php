<!-- Categories Section -->
<section class="bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-600">{{ __('Categories') }}</p>
            <h2 class="mt-1.5 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('Report What You See') }}</h2>
            <p class="mt-2 text-sm text-gray-500 sm:text-base">{{ __('Choose from these common street maintenance issues') }}</p>
        </div>

        <div class="grid gap-3 md:grid-cols-3 md:gap-5">
            <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-[0_3px_12px_rgba(17,24,39,0.04)] transition hover:border-gray-300 hover:shadow-md md:block md:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg md:mb-4 bg-red-50">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Road Damage') }}</h3>
                <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Potholes, cracks, uneven pavement, and other surface damage that poses safety risks.') }}</p>
                </div>
            </div>

            <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-[0_3px_12px_rgba(17,24,39,0.04)] transition hover:border-gray-300 hover:shadow-md md:block md:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg md:mb-4 bg-amber-50">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Street Lighting') }}</h3>
                <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Broken, malfunctioning, or missing street lights affecting pedestrian and driver safety.') }}</p>
                </div>
            </div>

            <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-[0_3px_12px_rgba(17,24,39,0.04)] transition hover:border-gray-300 hover:shadow-md md:block md:p-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg md:mb-4 bg-sky-50">
                    <svg class="h-5 w-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Infrastructure') }}</h3>
                <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Damaged signs, barriers, sidewalks, curbs, drainage issues, and other street infrastructure.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
