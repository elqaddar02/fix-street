<!-- How It Works Section -->
<section class="bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-600">{{ __('Process') }}</p>
            <h2 class="mt-1.5 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('How It Works') }}</h2>
            <p class="mt-2 text-sm text-gray-500 sm:text-base">{{ __('Simple steps to report and track street issues') }}</p>
        </div>

        <div class="flex justify-center items-center">
            <div class="grid w-full max-w-4xl grid-cols-1 gap-3 md:grid-cols-3 md:gap-5">
                <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 text-start shadow-[0_3px_12px_rgba(17,24,39,0.04)] md:flex-col md:items-center md:p-6 md:text-center">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full md:mb-4 bg-red-600 text-base font-semibold text-white">1</div>
                    <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Take a Photo') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Capture a clear image of the street issue you found.') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 text-start shadow-[0_3px_12px_rgba(17,24,39,0.04)] md:flex-col md:items-center md:p-6 md:text-center">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full md:mb-4 bg-amber-500 text-base font-semibold text-white">2</div>
                    <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Describe Issue') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Fill in category, location, and detailed description.') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 text-start shadow-[0_3px_12px_rgba(17,24,39,0.04)] md:flex-col md:items-center md:p-6 md:text-center">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full md:mb-4 bg-emerald-600 text-base font-semibold text-white">3</div>
                    <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900 md:text-lg">{{ __('Follow Up') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Neighbors can support the report and everyone can follow its status until it is fixed.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
