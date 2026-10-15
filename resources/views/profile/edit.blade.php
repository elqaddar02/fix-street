<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Profile') }}</h1>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:p-6">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
