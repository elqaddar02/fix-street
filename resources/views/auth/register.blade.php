@php
    $field = 'mt-1.5 block h-11 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
@endphp

<x-guest-layout :title="__('Register')">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Create your account') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Join Madinup and start reporting street issues.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <input id="name" class="{{ $field }}" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <input id="email" class="{{ $field }}" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" inputmode="email" dir="ltr">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-password-input id="password" autocomplete="new-password" required minlength="8" aria-describedby="password-hint" />
            <p id="password-hint" class="mt-1.5 text-xs text-gray-500">{{ __('signin.password_hint') }}</p>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-primary-button class="h-11 w-full">{{ __('Register') }}</x-primary-button>

        <p class="text-center text-xs leading-5 text-gray-500">
            {{ __('signin.terms_notice') }}
            <a href="{{ route('terms') }}" class="font-medium text-gray-700 underline-offset-2 hover:underline">{{ __('footer.terms_of_service') }}</a>
            ·
            <a href="{{ route('privacy') }}" class="font-medium text-gray-700 underline-offset-2 hover:underline">{{ __('footer.privacy_policy') }}</a>
        </p>
    </form>

    <p class="mt-6 border-t border-gray-100 pt-5 text-center text-sm text-gray-600">
        {{ __('Already registered?') }}
        <a href="{{ route('login') }}" class="font-semibold text-red-700 hover:underline">{{ __('Log in') }}</a>
    </p>
</x-guest-layout>
