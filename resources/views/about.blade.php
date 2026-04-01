@php($title = __('About Fix Street'))

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Fix Street') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-red-200 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 space-y-5 leading-relaxed">
                    <p>
                        {{ __('Madinova is a civic reporting platform that helps residents notify local authorities about damaged roads, broken streetlights, waste issues, and other public infrastructure problems.') }}
                    </p>
                    <p>
                        {{ __('Our mission is simple: make city maintenance more transparent and faster by connecting citizens, community feedback, and municipal follow-up in one place.') }}
                    </p>
                    <p>
                        {{ __('Every report includes category and location context so maintenance teams can prioritize urgent issues and update status as work progresses.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
