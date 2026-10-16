<x-guest-layout :title="__('signin.verify_title')">
    <div class="mb-6">
        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-red-50 text-red-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
        </span>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('signin.verify_title') }}</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-5" :status="__('A new verification link has been sent to the email address you provided during registration.')" />
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-primary-button class="h-11 w-full">{{ __('Resend Verification Email') }}</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6 border-t border-gray-100 pt-5 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900 hover:underline">{{ __('Log Out') }}</button>
    </form>
</x-guest-layout>
