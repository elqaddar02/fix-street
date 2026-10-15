@php($title = 'Help - Madinup')

<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Help & Support') }}</h1>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4" x-data="{ activeTab: 'help-citizens' }">
                <!-- Left Sidebar -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)] p-2 lg:sticky lg:top-24">
                        <nav>
                            <ul class="space-y-1">
                                <li>
                                    <button @click="activeTab = 'help-citizens'" :class="activeTab === 'help-citizens' ? 'bg-red-50 font-semibold text-red-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full rounded-lg px-3 py-2 text-start text-sm transition-colors">
                                        {{ __('help.tabs.citizens') }}
                                    </button>
                                </li>
                               
                                <li>
                                    <button @click="activeTab = 'conditions'" :class="activeTab === 'conditions' ? 'bg-red-50 font-semibold text-red-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full rounded-lg px-3 py-2 text-start text-sm transition-colors">
                                        {{ __('help.tabs.conditions') }}
                                    </button>
                                </li>
                                <li>
                                    <button @click="activeTab = 'privacy'" :class="activeTab === 'privacy' ? 'bg-red-50 font-semibold text-red-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full rounded-lg px-3 py-2 text-start text-sm transition-colors">
                                        {{ __('help.tabs.privacy') }}
                                    </button>
                                </li>
                                <li>
                                    <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'bg-red-50 font-semibold text-red-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'" class="w-full rounded-lg px-3 py-2 text-start text-sm transition-colors">
                                        {{ __('help.tabs.contact') }}
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="lg:col-span-3">
                    <!-- Help for Citizens Tab -->
                    <div x-show="activeTab === 'help-citizens'" x-cloak>
                        <x-help.citizens-tab />
                    </div>

                 

                    <!-- Conditions of Use Tab -->
                    <div x-show="activeTab === 'conditions'" x-cloak>
                        <x-help.conditions-tab />
                    </div>

                    <!-- Privacy Tab -->
                    <div x-show="activeTab === 'privacy'" x-cloak>
                        <x-help.privacy-tab />
                    </div>

                    <!-- Contact Tab -->
                    <div x-show="activeTab === 'contact'" x-cloak>
                        <x-help.contact-tab />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
