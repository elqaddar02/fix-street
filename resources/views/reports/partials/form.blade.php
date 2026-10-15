{{--
    The one report form, shared by create and edit so the two pages can't drift
    apart again. Element ids are what public/js/reports/create.js binds to.

    $report  null when creating
    $locked  true when the report is attached to a case: its photo and
             location are evidence and can no longer change
--}}
@php
    $report = $report ?? null;
    $locked = $locked ?? false;

    $field = 'mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500';
    $section = 'p-5 sm:p-6';
    $sectionTitle = 'text-base font-semibold text-gray-900';
    $sectionHint = 'mt-1 text-sm text-gray-500';
    $hasCurrentImage = $report?->image && file_exists(storage_path('app/public/' . $report->image));
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
        <p class="font-semibold">{{ __('Please fix the following errors:') }}</p>
        <ul class="mt-1.5 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]">

    {{-- PHOTO --}}
    <section class="{{ $section }}">
        <h2 class="{{ $sectionTitle }}">{{ $report ? __('Current Photo') : __('Upload Photo') }}</h2>

        @if($locked)
            <div class="mt-4 flex items-center gap-4">
                @if($hasCurrentImage)
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="h-20 w-20 shrink-0 rounded-lg object-cover ring-1 ring-gray-200">
                @endif
                <p class="text-sm text-gray-500">{{ __('This report is part of a case: its photo and location can no longer be changed.') }}</p>
            </div>
        @else
            <div class="mt-4 flex items-center gap-4">
                @if($hasCurrentImage)
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="h-14 w-14 shrink-0 rounded-lg object-cover ring-1 ring-gray-200">
                @endif
                {{-- Single row rather than a tall drop area: the photo is the
                     first field, so its height set the tone for the whole form. --}}
                <div class="relative flex min-w-0 flex-1 cursor-pointer items-center gap-3 rounded-lg border border-dashed border-gray-300 bg-gray-50/60 px-4 py-3 transition-colors hover:border-red-400 hover:bg-red-50/60" id="image-upload-area">
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />
                    <svg class="h-6 w-6 flex-none text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700">{{ $report ? __('Upload New Photo (optional)') : __('Click to upload or drag and drop') }}</p>
                        <p class="text-xs text-gray-500">{{ __('Photos from your phone are resized automatically.') }}</p>
                    </div>
                </div>
            </div>
            <p id="image-processing" class="mt-2 hidden text-sm text-gray-500">{{ __('Preparing photo...') }}</p>
            <div id="image-preview" class="mt-3 hidden">
                <img id="preview-image" src="" alt="{{ __('Preview') }}" class="max-h-32 rounded-lg ring-1 ring-gray-200">
            </div>
            <div id="image-alert" class="mt-3 hidden flex items-start gap-2.5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm text-rose-800">
                <svg class="mt-0.5 h-4 w-4 flex-none" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                <p id="image-alert-text"></p>
            </div>
            @error('image')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        @endif
    </section>

    {{-- DETAILS --}}
    <section class="{{ $section }} space-y-5">
        <div>
            <x-input-label for="title" :value="__('Describe the Problem')" />
            <input id="title" name="title" type="text" class="{{ $field }}"
                   placeholder="{{ __('Brief description of the problem') }}" value="{{ old('title', $report?->title) }}" required>
            @error('title')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <x-input-label for="category_id" :value="__('Category')" />
            <select id="category_id" name="category_id" class="{{ $field }}" required>
                <option value="">{{ __('-- Select a category --') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $report?->category_id) == $category->id)>
                        {{ $category->display_name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </section>

    {{-- LOCATION --}}
    <section class="{{ $section }}">
        <h2 class="{{ $sectionTitle }}">{{ __('Location') }}</h2>

        @if($locked)
            <p class="{{ $sectionHint }}">
                {{ collect([$report->city?->display_name, $report->district ? (app()->getLocale() === 'ar' ? $report->district->name_ar : $report->district->name_fr) : null])->filter()->implode(' · ') ?: __('dashboard.labels.unknown') }}
            </p>
        @else
            <p class="{{ $sectionHint }}">{{ __('If your photo has GPS data we\'ll detect the area automatically. Otherwise, pick it below.') }}</p>

            <!-- Always-visible location status: which source placed the pin -->
            <div id="location-status" class="mt-3 hidden">
                <p id="location-status-text"></p>
            </div>

            <!-- AUTO / LOCKED: shown once a photo's EXIF GPS resolves the hierarchy -->
            <div id="location-auto-panel" class="mt-4 hidden">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach(['auto-city-name' => __('City'), 'auto-district-name' => __('District / Arrondissement'), 'auto-quartier-name' => __('Quartier')] as $id => $label)
                        <div>
                            <x-input-label :value="$label" />
                            <div class="mt-1.5 flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">
                                <svg class="h-4 w-4 flex-none text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                <span id="{{ $id }}">—</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="edit-location-btn" class="mt-3 inline-flex items-center text-sm font-semibold text-red-700 underline-offset-2 hover:underline">
                    {{ __('Modifier la localisation') }}
                </button>

                <input type="hidden" name="city_id" id="auto-city-id" value="{{ old('city_id') }}">
                <input type="hidden" name="district_id" id="auto-district-id" value="{{ old('district_id') }}">
                <input type="hidden" name="quartier_id" id="auto-quartier-id" value="{{ old('quartier_id') }}">
            </div>

            <!-- MANUAL: City -> District -> Quartier cascade, the reliable fallback -->
            <fieldset id="location-manual-fieldset" class="m-0 mt-4 border-0 p-0">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <x-input-label for="city_id" :value="__('City')" />
                        <select id="city_id" name="city_id" class="{{ $field }}">
                            <option value="">{{ __('-- Select a city --') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" data-lat="{{ $city->latitude }}" data-lng="{{ $city->longitude }}" @selected(old('city_id', $report?->city_id) == $city->id)>
                                    {{ $city->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="district_id" :value="__('District / Arrondissement')" />
                        <select id="district_id" name="district_id" class="{{ $field }}" disabled>
                            <option value="">{{ __('-- Select a district --') }}</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}" data-city-id="{{ $district->city_id }}" data-lat="{{ $district->lat }}" data-lng="{{ $district->lng }}" @selected(old('district_id', $report?->district_id) == $district->id)>
                                    {{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="quartier_id" :value="__('Quartier')" />
                        <select id="quartier_id" name="quartier_id" class="{{ $field }}" disabled>
                            <option value="">{{ __('-- Select a quartier --') }}</option>
                            @foreach ($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" data-district-id="{{ $quartier->district_id }}" data-lat="{{ $quartier->latitude }}" data-lng="{{ $quartier->longitude }}" @selected(old('quartier_id', $report?->quartier_id) == $quartier->id)>
                                    {{ app()->getLocale() === 'ar' ? $quartier->name_ar : $quartier->name_fr }}
                                </option>
                            @endforeach
                        </select>
                        @error('quartier_id')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <p id="location-area" class="mt-3 hidden text-sm text-gray-500"></p>

            <div id="map" class="mt-4 h-80 w-full rounded-lg border border-gray-200"></div>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end">
                <button type="button" id="use-location-btn" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    {{ __('Use My Location') }}
                </button>
                <div class="flex flex-1 gap-3">
                    <div class="flex-1">
                        <x-input-label for="latitude" :value="__('Latitude')" />
                        <input id="latitude" name="latitude" type="text" dir="ltr" class="{{ $field }} bg-gray-50 text-gray-600"
                               placeholder="33.9716" value="{{ old('latitude', $report?->latitude) }}" readonly>
                        @error('latitude')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1">
                        <x-input-label for="longitude" :value="__('Longitude')" />
                        <input id="longitude" name="longitude" type="text" dir="ltr" class="{{ $field }} bg-gray-50 text-gray-600"
                               placeholder="-6.8498" value="{{ old('longitude', $report?->longitude) }}" readonly>
                        @error('longitude')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">{{ __('Click on the map to set the exact location, or use your current location.') }}</p>
        @endif
    </section>

    {{-- ACTIONS --}}
    <div class="flex items-center justify-end gap-3 rounded-b-xl bg-gray-50 px-5 py-4 sm:px-6">
        <a href="{{ $report ? route('reports.show', $report) : route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
            {{ __('Cancel') }}
        </a>
        <x-primary-button>{{ $report ? __('Update Report') : __('Submit Report') }}</x-primary-button>
    </div>
</div>
