@php
    $title = __('Edit Report') . ' - Madinup';
    // A report that is part of a case is evidence: its photo and location can no longer change.
    $locked = (bool) $report->dossier_id;
@endphp

<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-6">
            <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900">
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                {{ __('Back') }}
            </a>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900">{{ __('Edit Report') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('Update the details of your street maintenance report.') }}</p>
        </header>

        <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('reports.partials.form', ['report' => $report, 'locked' => $locked])
        </form>
    </div>

    @unless($locked)
        @include('reports.partials.form-scripts')
    @endunless
</x-app-layout>
