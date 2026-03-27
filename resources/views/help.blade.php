@php($title = 'Help - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Help & Support') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ tab: 'rules' }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button @click="tab='rules'" :class="tab==='rules' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'" class="rounded-md px-4 py-2 transition">House Rules</button>
                        <button @click="tab='faq'" :class="tab==='faq' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'" class="rounded-md px-4 py-2 transition">FAQ</button>
                        <button @click="tab='contact'" :class="tab==='contact' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'" class="rounded-md px-4 py-2 transition">Contact</button>
                        <button @click="tab='other'" :class="tab==='other' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700'" class="rounded-md px-4 py-2 transition">Other</button>
                    </div>

                    <div x-show="tab==='rules'" x-cloak>
                        <h3 class="text-lg font-semibold mb-3">FixMyStreet Conditions of Use</h3>
                        <p class="mb-2">By posting reports and comments on Madinova, you agree to:</p>
                        <ul class="list-disc pl-5 text-gray-700 space-y-2 mb-4">
                            <li>Submit only accurate and respectful information.</li>
                            <li>Avoid abusive language, false reports, spam, or unlawful content.</li>
                            <li>Provide lawful, verifiable evidence for public infrastructure issues.</li>
                            <li>Keep community interaction professional and constructive.</li>
                        </ul>
                        <p class="text-sm text-gray-500">Violations may result in moderation action, including report removal or account restrictions.</p>
                    </div>

                    <div x-show="tab==='faq'" x-cloak>
                        <h3 class="text-lg font-semibold mb-3">Frequently Asked Questions</h3>
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold">How can I submit a report?</h4>
                                <p class="text-gray-700">Use the "All Reports" tab then click "Create Report" to provide issue details, location, and photos.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold">Can I edit or delete my report?</h4>
                                <p class="text-gray-700">Currently admins handle moderation. Contact us via the Contact tab if you need updates.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold">How long does it take city teams to respond?</h4>
                                <p class="text-gray-700">Response times vary by municipality. Use report status updates in the portal to track progress.</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab==='contact'" x-cloak>
                        <h3 class="text-lg font-semibold mb-3">Contact Us</h3>
                        <p class="text-gray-700 mb-3">For support, email <a href="mailto:support@madinova.local" class="text-indigo-600">support@madinova.local</a> or use the contact page.</p>
                        <p class="text-gray-700">Phone: +212 123 456 789</p>
                    </div>

                    <div x-show="tab==='other'" x-cloak>
                        <h3 class="text-lg font-semibold mb-3">Other Help Links</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-700">
                            <li><a href="{{ route('privacy') }}" class="text-indigo-600">Privacy Policy</a></li>
                            <li><a href="{{ route('terms') }}" class="text-indigo-600">Terms of Service</a></li>
                            <li><a href="{{ route('contact') }}" class="text-indigo-600">Contact Page</a></li>
                            <li><a href="{{ route('dashboard') }}" class="text-indigo-600">Dashboard (if logged in)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
