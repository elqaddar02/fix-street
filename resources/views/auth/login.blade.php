@php
    $field = 'mt-1.5 block h-11 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
@endphp

<x-guest-layout :title="__('Log in')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Login to your account') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Welcome back! Please sign in to continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <input id="email" class="{{ $field }}" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email" dir="ltr">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-red-700 hover:underline" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif
            </div>
            <x-password-input id="password" autocomplete="current-password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <label for="remember_me" class="flex w-fit cursor-pointer items-center gap-2.5">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
            <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
        </label>

        <x-primary-button class="h-11 w-full">{{ __('Log in') }}</x-primary-button>
    </form>

    <p class="mt-6 border-t border-gray-100 pt-5 text-center text-sm text-gray-600">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold text-red-700 hover:underline">{{ __('Register here') }}</a>
    </p>
</x-guest-layout>
