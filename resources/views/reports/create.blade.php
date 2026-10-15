@php($title = __('Report a Street Issue') . ' - Madinup')

<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900">
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                {{ __('Back') }}
            </a>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Report a Street Issue') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('Add a photo, describe the problem, and drop a pin — we\'ll figure out the rest.') }}</p>
        </header>

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf
            @include('reports.partials.form')
        </form>
    </div>

    @include('reports.partials.form-scripts')
</x-app-layout>
