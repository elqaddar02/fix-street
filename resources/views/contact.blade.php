@php($title = __('Contact') . ' - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-red-200 shadow-sm sm:rounded-lg">
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
