{{-- Confirms the last action ("Report submitted", "Message sent"…), or explains
     why it didn't go through (e.g. an expired form), then fades away. --}}
@php
    // Laravel's email-verification link lands here with ?verified=1.
    $message = session('success') ?? (request()->boolean('verified') ? __('signin.email_verified') : null);
    $isNotice = false;

    // A refusal ("you can't confirm your own report") reads as a notice, not a success.
    if (! $message && (session('notice') || session('error'))) {
        $message = session('notice') ?? session('error');
        $isNotice = true;
    }
@endphp

@if($message)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition.opacity.duration.300ms
         class="pointer-events-none fixed inset-x-0 top-20 z-[60] flex justify-center px-4">
        <div role="status" @class([
            'pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-lg border bg-white px-4 py-3 text-sm text-gray-800 shadow-lg',
            'border-emerald-200' => ! $isNotice,
            'border-amber-200' => $isNotice,
        ])>
            @if($isNotice)
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
            @else
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            @endif
            <p class="min-w-0 flex-1 font-medium">{{ $message }}</p>
            <button type="button" @click="show = false" class="-m-1 rounded-md p-1 text-gray-400 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                <span class="sr-only">{{ __('Close') }}</span>
            </button>
        </div>
    </div>
@endif
