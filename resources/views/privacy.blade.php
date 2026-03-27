@php($title = 'Privacy Policy - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Privacy Policy
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-red-200 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 space-y-5 leading-relaxed">
                    <p>
                        We collect account data (such as name and email), report details, and optional uploaded images to operate
                        the Madinova platform and improve maintenance response quality.
                    </p>
                    <p>
                        We use this information to authenticate users, display reports, prevent abuse, and support issue moderation.
                        We do not sell personal information.
                    </p>
                    <p>
                        Madinova may use cookies and similar technologies for session management, security, and analytics. If
                        Google AdSense is enabled, Google may use cookies to serve personalized or non-personalized ads based on
                        your browsing context.
                    </p>
                    <p>
                        Third-party vendors, including Google, may use cookies to show ads. Users can manage ad personalization
                        settings through Google's Ads Settings and browser cookie controls.
                    </p>
                    <p>
                        If you need data correction or deletion assistance, contact us through the Contact page.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
