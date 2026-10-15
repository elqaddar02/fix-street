<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('help.contact.title') }}</h1>
        <p class="text-gray-600">{{ __('help.contact.subtitle') }}</p>
    </div>

    <!-- Contact Form Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)] p-5 sm:p-6">
        <form action="#" method="POST" class="space-y-6">
            <!-- Topic Radio Buttons -->
            <fieldset>
                <legend class="text-lg font-semibold text-gray-900 mb-4">{{ __('help.contact.contact_form.topic') }}</legend>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input id="topic-platform" name="topic" type="radio" value="platform" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-platform" class="ml-2 text-gray-700">{{ __('help.contact.contact_form.topic_platform') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-feedback" name="topic" type="radio" value="feedback" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-feedback" class="ml-2 text-gray-700">{{ __('help.contact.contact_form.topic_feedback') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-authority" name="topic" type="radio" value="authority" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-authority" class="ml-2 text-gray-700">{{ __('help.contact.contact_form.topic_authority') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-report" name="topic" type="radio" value="report" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-report" class="ml-2 text-gray-700">{{ __('help.contact.contact_form.topic_report') }}</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-not-fixed" name="topic" type="radio" value="not-fixed" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-not-fixed" class="ml-2 text-gray-700">{{ __('help.contact.contact_form.topic_not_fixed') }}</label>
                    </div>
                </div>
            </fieldset>

            <!-- Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('help.contact.contact_form.name') }}</label>
                    <input type="text" id="name" name="name" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('help.contact.contact_form.email') }}</label>
                    <input type="email" id="email" name="email" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('help.contact.contact_form.phone') }}</label>
                    <input type="tel" id="phone" name="phone" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">{{ __('help.contact.contact_form.subject') }}</label>
                    <input type="text" id="subject" name="subject" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">{{ __('help.contact.contact_form.message') }}</label>
                <textarea id="message" name="message" rows="4" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
            </div>

            <!-- Submit Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <input id="captcha" name="captcha" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300 rounded">
                    <label for="captcha" class="ml-2 text-sm text-gray-700">{{ __('help.contact.contact_form.captcha') }}</label>
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                    {{ __('help.contact.contact_form.send') }}
                </button>
            </div>
        </form>
    </div>
</div>
