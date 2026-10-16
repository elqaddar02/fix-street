@php
    $field = 'block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
    $topics = [
        'platform' => 'help.contact.contact_form.topic_platform',
        'feedback' => 'help.contact.contact_form.topic_feedback',
        'authority' => 'help.contact.contact_form.topic_authority',
        'report' => 'help.contact.contact_form.topic_report',
        'not-fixed' => 'help.contact.contact_form.topic_not_fixed',
    ];
@endphp

<div class="space-y-5">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">{{ __('help.contact.title') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('help.contact.subtitle') }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-[0_3px_12px_rgba(17,24,39,0.04)] sm:p-6">
        @unless(config('app.contact_email'))
            <p class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-sm text-amber-800">{{ __('help.contact.unavailable') }}</p>
        @endunless

        <form action="{{ route('help.contact') }}" method="POST" class="space-y-5">
            @csrf

            <fieldset>
                <legend class="text-base font-semibold text-gray-900">{{ __('help.contact.contact_form.topic') }}</legend>
                <div class="mt-3 space-y-1">
                    @foreach($topics as $value => $key)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-gray-50">
                            <input name="topic" type="radio" value="{{ $value }}" @checked(old('topic', 'platform') === $value) required
                                   class="h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-gray-700">{{ __($key) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('topic') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </fieldset>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach([
                    ['name', 'text', 'help.contact.contact_form.name', true, 'name'],
                    ['email', 'email', 'help.contact.contact_form.email', true, 'email'],
                    ['phone', 'tel', 'help.contact.contact_form.phone', false, 'tel'],
                    ['subject', 'text', 'help.contact.contact_form.subject', true, 'off'],
                ] as [$name, $type, $labelKey, $required, $autocomplete])
                    <div>
                        <label for="contact-{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __($labelKey) }}</label>
                        <input type="{{ $type }}" id="contact-{{ $name }}" name="{{ $name }}" value="{{ old($name) }}" autocomplete="{{ $autocomplete }}"
                               @if($required) required @endif class="{{ $field }}" @if($type === 'tel' || $type === 'email') dir="ltr" @endif>
                        @error($name) <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            <div>
                <label for="contact-message" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('help.contact.contact_form.message') }}</label>
                <textarea id="contact-message" name="message" rows="5" required maxlength="5000" class="{{ $field }}">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- Honeypot: invisible to people, so anything typed here came from a bot. --}}
            <div class="hidden" aria-hidden="true">
                <label for="contact-website">Website</label>
                <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-4">
                <x-primary-button class="w-full sm:w-auto">{{ __('help.contact.contact_form.send') }}</x-primary-button>
            </div>
        </form>
    </div>
</div>
