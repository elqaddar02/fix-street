@php($title = 'Terms of Service - Madinup')

<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Terms of Service') }}</h1>

            <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                <div class="p-6">
                    @include('termscontent')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
