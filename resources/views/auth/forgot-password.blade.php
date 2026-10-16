<x-guest-layout :title="__('Forgot your password?')">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Forgot your password?') }}</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">{{ __('signin.forgot_subtitle') }}</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email" dir="ltr"
                   class="mt-1.5 block h-11 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button class="h-11 w-full">{{ __('Email Password Reset Link') }}</x-primary-button>
    </form>

    @include('auth.partials.back-to-login')
</x-guest-layout>
