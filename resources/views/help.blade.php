@php
    $title = __('Help & Support') . ' - Madinup';

    $tabs = [
        'help-citizens' => __('help.tabs.citizens'),
        'conditions' => __('help.tabs.conditions'),
        'privacy' => __('help.tabs.privacy'),
        'contact' => __('help.tabs.contact'),
    ];

    // Open on the contact form after sending it (or when it bounced back with errors).
    $initialTab = $errors->any() ? 'contact' : request('tab', 'help-citizens');
    if (! array_key_exists($initialTab, $tabs)) {
        $initialTab = 'help-citizens';
    }
@endphp

<x-app-layout>

    <div class="py-6 lg:py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-4 text-2xl font-semibold tracking-tight text-gray-900 lg:mb-6">{{ __('Help & Support') }}</h1>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-6" x-data="{ activeTab: @js($initialTab) }">
                {{--
                    Desktop: a sticky list in a sidebar. Phones: a row of chips
                    that scrolls sideways, so the content starts straight away.
                --}}
                <div class="lg:col-span-1">
                    <nav aria-label="{{ __('Help & Support') }}" x-init="$nextTick(() => $el.querySelector('[aria-selected=true]')?.scrollIntoView({ inline: 'center', block: 'nearest' }))"
                         class="-mx-4 overflow-x-auto px-4 [scrollbar-width:none] lg:sticky lg:top-24 lg:mx-0 lg:overflow-visible lg:rounded-xl lg:border lg:border-gray-200 lg:bg-white lg:p-2 lg:shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
                        <ul class="flex gap-2 lg:flex-col lg:gap-1" role="tablist">
                            @foreach($tabs as $key => $tabLabel)
                                <li class="shrink-0">
                                    <button type="button" role="tab" @click="activeTab = @js($key)"
                                            :aria-selected="activeTab === @js($key) ? 'true' : 'false'"
                                            :class="activeTab === @js($key)
                                                ? 'border-red-200 bg-red-50 font-semibold text-red-700'
                                                : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 lg:border-transparent lg:bg-transparent'"
                                            class="whitespace-nowrap rounded-lg border px-3.5 py-2 text-start text-sm transition-colors lg:w-full lg:px-3">
                                        {{ $tabLabel }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
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
