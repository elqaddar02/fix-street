<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Create your account') }}</h2>
        <p class="text-gray-600 mt-2">{{ __('Join Madinova and start reporting street issues.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center rounded-xl bg-red-600 hover:bg-red-700 focus:ring-red-500 active:bg-red-800 py-3 text-sm">
            {{ __('Register') }}
        </x-primary-button>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700 font-semibold">
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
