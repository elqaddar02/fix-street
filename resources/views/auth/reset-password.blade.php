<x-guest-layout :title="__('Reset Password')">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('signin.reset_title') }}</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">{{ __('signin.reset_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username" inputmode="email" dir="ltr"
                   class="mt-1.5 block h-11 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-password-input id="password" autocomplete="new-password" required minlength="8" autofocus aria-describedby="password-hint" />
            <p id="password-hint" class="mt-1.5 text-xs text-gray-500">{{ __('signin.password_hint') }}</p>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-primary-button class="h-11 w-full">{{ __('Reset Password') }}</x-primary-button>
    </form>

    @include('auth.partials.back-to-login')
</x-guest-layout>
