<!-- How It Works Section -->
<section class="bg-gray-50 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-8 max-w-2xl text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-600">{{ __('Process') }}</p>
            <h2 class="mt-1.5 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('How It Works') }}</h2>
            <p class="mt-2 text-sm text-gray-500 sm:text-base">{{ __('Simple steps to report and track street issues') }}</p>
        </div>

        <div class="flex justify-center items-center">
            <div class="grid w-full max-w-4xl grid-cols-1 gap-5 md:grid-cols-3">
                <div class="flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-600 text-base font-semibold text-white">1</div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Take a Photo') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Capture a clear image of the street issue you found.') }}</p>
                </div>
                <div class="flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-amber-500 text-base font-semibold text-white">2</div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Describe Issue') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Fill in category, location, and detailed description.') }}</p>
                </div>
                <div class="flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-600 text-base font-semibold text-white">3</div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Follow Up') }}</h3>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500">{{ __('Neighbors can support the report and everyone can follow its status until it is fixed.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
