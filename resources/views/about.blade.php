@php($title = __('About Madinup'))

<x-app-layout>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">{{ __('About Madinup') }}</h1>

            <div class="rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]">
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
