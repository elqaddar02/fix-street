<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Technical Support</h1>
        <p class="text-gray-600">Get help with using the platform or report technical issues. Our team is here to assist you.</p>
    </div>

    <!-- Contact Form Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="#" method="POST" class="space-y-6">
            <!-- Topic Radio Buttons -->
            <fieldset>
                <legend class="text-lg font-semibold text-gray-900 mb-4">Topic</legend>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input id="topic-platform" name="topic" type="radio" value="platform" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-platform" class="ml-2 text-gray-700">Help using the platform</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-feedback" name="topic" type="radio" value="feedback" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-feedback" class="ml-2 text-gray-700">Feedback</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-authority" name="topic" type="radio" value="authority" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-authority" class="ml-2 text-gray-700">Authority question</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-report" name="topic" type="radio" value="report" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-report" class="ml-2 text-gray-700">Report a problem</label>
                    </div>
                    <div class="flex items-center">
                        <input id="topic-not-fixed" name="topic" type="radio" value="not-fixed" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300">
                        <label for="topic-not-fixed" class="ml-2 text-gray-700">Problem not fixed</label>
                    </div>
                </div>
            </fieldset>

            <!-- Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500">
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500">
                </div>
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea id="message" name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500"></textarea>
            </div>

            <!-- Submit Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <input id="captcha" name="captcha" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-200 border-gray-300 rounded">
                    <label for="captcha" class="ml-2 text-sm text-gray-700">I'm not a robot</label>
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
