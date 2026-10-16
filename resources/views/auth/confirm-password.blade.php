<x-guest-layout :title="__('signin.confirm_title')">
    <div class="mb-6">
        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-red-50 text-red-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
        </span>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('signin.confirm_title') }}</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-password-input id="password" autocomplete="current-password" required autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <x-primary-button class="h-11 w-full">{{ __('Confirm') }}</x-primary-button>
    </form>
</x-guest-layout>
