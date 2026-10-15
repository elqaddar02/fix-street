@php($title = __('Contact') . ' - Madinup')

<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Contact') }}</h1>

            <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                <div class="p-6 text-gray-800 space-y-5 leading-relaxed">
                    <p>
                        {{ __('Need help with a report or account access? Reach our support team and we will respond as soon as possible.') }}
                    </p>
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p><strong>{{ __('Email:') }}</strong> {{ __('support@fixstreet.local') }}</p>
                        <p><strong>{{ __('Office Hours:') }}</strong> {{ __('Monday - Friday, 09:00 - 17:00') }}</p>
                        <p><strong>{{ __('Response Time:') }}</strong> {{ __('Usually within 1-2 business days') }}</p>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ __('For emergencies or immediate public safety concerns, please contact your local emergency or municipal hotline.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
