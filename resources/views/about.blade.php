@php($title = __('About Madinup'))

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Madinup') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-red-200 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 space-y-5 leading-relaxed">
                    <p>
                        {{ __('Madinup is an independent citizen platform that helps residents make street problems visible (damaged roads, broken streetlights, waste and other public infrastructure issues) and follow them until they are resolved.') }}
                    </p>
                    <p>
                        {{ __('Our mission is to make street problems visible, bring neighbors together around them, and keep a clear public record of what happens next.') }}
                    </p>
                    <p>
                        {{ __('Madinup is not a government service and is not affiliated with any public authority. Publishing a report here is not an official complaint.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
