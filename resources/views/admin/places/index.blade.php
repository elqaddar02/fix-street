@extends('admin.layouts.app')

@section('title', __('admin.places_title', ['city' => $city->name]))

@php
    $isAr = app()->getLocale() === 'ar';
    $iconButton = 'rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700';
    $editIcon = 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
    $trashIcon = 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.cities.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900">
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                {{ __('admin.cities') }}
            </a>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $city->name }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ __('admin.places_intro') }}</p>
        </div>
        <a href="{{ route('admin.districts.create', $city) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('admin.districts_new') }}
        </a>
    </div>

    @forelse($districts as $district)
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            {{-- District --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-slate-200 bg-slate-50/80 px-5 py-3">
                <div class="min-w-0 basis-full sm:basis-0 sm:flex-1">
                    <h3 class="text-sm font-semibold text-slate-900">
                        {{ $isAr ? $district->name_ar : $district->name_fr }}
                        <span class="ms-1 font-normal text-slate-400">{{ $isAr ? $district->name_fr : $district->name_ar }}</span>
                    </h3>
                    <p class="mt-0.5 flex flex-wrap items-center gap-x-3 text-xs text-slate-500">
                        <span>{{ __('admin.reports_count', ['count' => $district->reports_count]) }}</span>
                        <span>{{ __('admin.quartiers_count', ['count' => $district->quartiers->count()]) }}</span>
                        @include('admin.places._position', ['lat' => $district->lat, 'lng' => $district->lng])
                    </p>
                </div>
                <div class="-ms-1 flex items-center gap-1 sm:ms-0">
                    <a href="{{ route('admin.quartiers.create', $district) }}"
                       class="me-1 inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        {{ __('admin.quartiers_new') }}
                    </a>
                    <a href="{{ route('admin.districts.edit', $district) }}" class="{{ $iconButton }}" title="{{ __('admin.edit') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $editIcon }}"/></svg>
                        <span class="sr-only">{{ __('admin.edit_name', ['name' => $district->name_fr]) }}</span>
                    </a>
                    @include('admin.places._delete', [
                        'action' => route('admin.districts.destroy', $district),
                        'name' => $district->name_fr,
                        'blocked' => $district->reports_count > 0 || $district->quartiers->isNotEmpty(),
                        'reason' => $district->quartiers->isNotEmpty()
                            ? __('admin.district_has_quartiers')
                            : __('admin.used_by_reports', ['count' => $district->reports_count]),
                    ])
                </div>
            </div>

            {{-- Quartiers --}}
            <ul class="divide-y divide-slate-100">
                @forelse($district->quartiers as $quartier)
                    <li class="flex flex-wrap items-center gap-x-4 gap-y-1 px-5 py-2.5 transition-colors hover:bg-slate-50/60">
                        <div class="min-w-0 basis-full sm:basis-0 sm:flex-1">
                            <p class="truncate text-sm text-slate-900">
                                {{ $isAr ? ($quartier->name_ar ?: $quartier->name_fr) : $quartier->name_fr }}
                                <span class="ms-1 text-slate-400">{{ $isAr ? $quartier->name_fr : $quartier->name_ar }}</span>
                            </p>
                        </div>
                        <span class="text-xs tabular-nums text-slate-500">{{ __('admin.reports_count', ['count' => $quartier->reports_count]) }}</span>
                        <span class="text-xs">@include('admin.places._position', ['lat' => $quartier->latitude, 'lng' => $quartier->longitude])</span>
                        <div class="ms-auto flex items-center gap-1 sm:ms-0">
                            <a href="{{ route('admin.quartiers.edit', $quartier) }}" class="{{ $iconButton }}" title="{{ __('admin.edit') }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $editIcon }}"/></svg>
                                <span class="sr-only">{{ __('admin.edit_name', ['name' => $quartier->name_fr]) }}</span>
                            </a>
                            @include('admin.places._delete', [
                                'action' => route('admin.quartiers.destroy', $quartier),
                                'name' => $quartier->name_fr,
                                'blocked' => $quartier->reports_count > 0,
                                'reason' => __('admin.used_by_reports', ['count' => $quartier->reports_count]),
                            ])
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-4 text-sm text-slate-400">{{ __('admin.quartiers_empty') }}</li>
                @endforelse
            </ul>
        </section>
    @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <p class="text-sm font-medium text-slate-600">{{ __('admin.districts_empty') }}</p>
            <p class="mt-1 text-sm text-slate-400">{{ __('admin.districts_empty_hint') }}</p>
        </div>
    @endforelse
</div>
@endsection
