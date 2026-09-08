<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Login to your account') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('Welcome back! Please sign in to continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" name="remember">
                <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-red-600 hover:text-red-700" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center rounded-xl bg-red-600 hover:bg-red-700 focus:ring-red-500 active:bg-red-800 py-3 text-sm">
            {{ __('Log in') }}
        </x-primary-button>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="text-red-600 hover:text-red-700 font-semibold">
                    {{ __('Register here') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
