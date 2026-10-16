{{-- Shown on every page to a signed-in user who hasn't confirmed their email yet,
     so it's clear why "New report" and "I confirm" send them elsewhere. --}}
@auth
    @if(! auth()->user()->hasVerifiedEmail() && ! request()->routeIs('verification.*'))
        <div class="border-b border-amber-200 bg-amber-50">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-3 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="flex items-start gap-2">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    <span>{{ __('signin.verify_banner', ['email' => auth()->user()->email]) }}</span>
                </p>
                @if(session('status') === 'verification-link-sent')
                    <p class="shrink-0 font-semibold text-emerald-700" role="status">{{ __('signin.link_sent') }}</p>
                @else
                    <form method="POST" action="{{ route('verification.send') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="font-semibold text-amber-900 underline underline-offset-2 hover:text-amber-700">{{ __('signin.resend_link') }}</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
@endauth
