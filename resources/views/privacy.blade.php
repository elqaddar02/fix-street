@php($title = __('Privacy Policy') . ' - Madinup')

<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Privacy Policy') }}</h1>

            <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                <div class="p-6 text-gray-800 space-y-5 leading-relaxed">
                    <p>
                        {{ __('We collect account data (such as name and email), report details, and optional uploaded images to operate the Madinup platform and improve maintenance response quality.') }}
                    </p>
                    <p>
                        {{ __('We use this information to authenticate users, display reports, prevent abuse, and support issue moderation. We do not sell personal information.') }}
                    </p>
                    <p>
                        {{ __('Madinup may use cookies and similar technologies for session management, security, and analytics. If Google AdSense is enabled, Google may use cookies to serve personalized or non-personalized ads based on your browsing context.') }}
                    </p>
                    <p>
                        {{ __('Third-party vendors, including Google, may use cookies to show ads. Users can manage ad personalization settings through Google\'s Ads Settings and browser cookie controls.') }}
                    </p>
                    <p>
                        {{ __('If you need data correction or deletion assistance, contact us through the Contact page.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
