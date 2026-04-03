@php($title = 'Help - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Help & Support') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 min-h-96" x-data="{ activeTab: 'help-citizens' }">
                <!-- Left Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-yellow-50 rounded-lg p-6 h-full">
                        <nav>
                            <ul class="space-y-2">
                                <li>
                                    <button @click="activeTab = 'help-citizens'" :class="{ 'bg-yellow-400 font-bold border-l-4 border-red-600': activeTab === 'help-citizens' }" class="w-full text-left px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                                        Help for citizens
                                    </button>
                                </li>
                               
                                <li>
                                    <button @click="activeTab = 'conditions'" :class="{ 'bg-yellow-400 font-bold border-l-4 border-red-600': activeTab === 'conditions' }" class="w-full text-left px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                                        Conditions of Use
                                    </button>
                                </li>
                                <li>
                                    <button @click="activeTab = 'privacy'" :class="{ 'bg-yellow-400 font-bold border-l-4 border-red-600': activeTab === 'privacy' }" class="w-full text-left px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                                        Privacy
                                    </button>
                                </li>
                                <li>
                                    <button @click="activeTab = 'contact'" :class="{ 'bg-yellow-400 font-bold border-l-4 border-red-600': activeTab === 'contact' }" class="w-full text-left px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                                        Contact
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
