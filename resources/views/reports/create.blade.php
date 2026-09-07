<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Report') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Report a Street Issue') }}</h3>
                        <p class="text-gray-600 mt-1">{{ __('Add a photo, describe the problem, and drop a pin — we\'ll figure out the rest.') }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border-l-4 border-red-600 bg-red-50 p-4 text-red-700">
                            <p class="font-semibold mb-2">{{ __('Please fix the following errors:') }}</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Image Upload (FIRST FIELD - REQUIRED) -->
                        <div>
                            <x-input-label for="image" :value="__('Upload Photo')" class="text-lg font-semibold" />
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition hover:border-red-500 hover:bg-red-50 cursor-pointer" id="image-upload-area">
                                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold">{{ __('Click to upload or drag and drop') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('JPG, JPEG, PNG up to 6MB') }}</p>
                                    </div>
                                </div>
                                <div id="image-preview" class="mt-4 hidden">
                                    <img id="preview-image" src="" alt="Preview" class="max-h-48 rounded-lg shadow-md mx-auto">
                                </div>
                                <div id="image-alert" class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-start gap-3">
                                    <svg class="w-5 h-5 flex-none text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0-12.728l12.728 12.728" />
                                    </svg>
                                    <p id="image-alert-text"></p>
                                </div>
                                @if($errors->has('image'))
                                    <div class="mt-2 text-sm text-red-600">
                                        {{ $errors->first('image') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Describe the Problem')" class="text-lg font-semibold" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4"
                                placeholder="{{ __('Brief description of the problem') }}" :value="old('title')" required />
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" class="text-lg font-semibold" />
                            <select id="category_id" name="category_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                <option value="">{{ __('-- Select a category --') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                        {{ $category->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label :value="__('Location')" class="text-lg font-semibold" />
                            <p class="text-sm text-gray-500 mt-1">{{ __('If your photo has GPS data we\'ll detect the area automatically. Otherwise, pick it below.') }}</p>

                            <!-- Always-visible location status: which source placed the pin -->
                            <div id="location-status" class="mt-3 hidden rounded-2xl border px-4 py-3 text-sm flex items-start gap-3">
                                <p id="location-status-text"></p>
                            </div>

                            <!-- AUTO / LOCKED: shown once a photo's EXIF GPS resolves the hierarchy -->
                            <div id="location-auto-panel" class="hidden mt-3">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label :value="__('City')" class="text-sm font-medium" />
                                        <div class="mt-1 flex items-center gap-2 rounded-lg border-2 border-gray-200 bg-gray-100 py-2.5 px-4 text-gray-700">
                                            <svg class="w-4 h-4 flex-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                            <span id="auto-city-name">—</span>
                                        </div>
                                    </div>
                                    <div>
                                        <x-input-label :value="__('District / Arrondissement')" class="text-sm font-medium" />
                                        <div class="mt-1 flex items-center gap-2 rounded-lg border-2 border-gray-200 bg-gray-100 py-2.5 px-4 text-gray-700">
                                            <svg class="w-4 h-4 flex-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                            <span id="auto-district-name">—</span>
                                        </div>
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Quartier')" class="text-sm font-medium" />
                                        <div class="mt-1 flex items-center gap-2 rounded-lg border-2 border-gray-200 bg-gray-100 py-2.5 px-4 text-gray-700">
                                            <svg class="w-4 h-4 flex-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                            <span id="auto-quartier-name">—</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="edit-location-btn" class="mt-3 inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-700 underline">
                                    {{ __('Modifier la localisation') }}
                                </button>

                                <input type="hidden" name="city_id" id="auto-city-id" value="{{ old('city_id') }}">
                                <input type="hidden" name="district_id" id="auto-district-id" value="{{ old('district_id') }}">
                                <input type="hidden" name="quartier_id" id="auto-quartier-id" value="{{ old('quartier_id') }}">
                            </div>

                            <!-- MANUAL: existing City -> District -> Quartier cascade, the reliable fallback -->
                            <fieldset id="location-manual-fieldset" class="mt-3 border-0 p-0 m-0">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="city_id" :value="__('City')" class="text-sm font-medium" />
                                        <select id="city_id" name="city_id" class="mt-1 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4">
                                            <option value="">{{ __('-- Select a city --') }}</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" data-lat="{{ $city->latitude }}" data-lng="{{ $city->longitude }}" @selected(old('city_id') == $city->id)>
                                                    {{ $city->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="district_id" :value="__('District / Arrondissement')" class="text-sm font-medium" />
                                        <select id="district_id" name="district_id" class="mt-1 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4" disabled>
                                            <option value="">{{ __('-- Select a district --') }}</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}" data-city-id="{{ $district->city_id }}" data-lat="{{ $district->lat }}" data-lng="{{ $district->lng }}" @selected(old('district_id') == $district->id)>
                                                    {{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('district_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="quartier_id" :value="__('Quartier')" class="text-sm font-medium" />
                                        <select id="quartier_id" name="quartier_id" class="mt-1 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-2.5 px-4" disabled>
                                            <option value="">{{ __('-- Select a quartier --') }}</option>
                                            @foreach ($quartiers as $quartier)
                                                <option value="{{ $quartier->id }}" data-district-id="{{ $quartier->district_id }}" data-lat="{{ $quartier->latitude }}" data-lng="{{ $quartier->longitude }}" @selected(old('quartier_id') == $quartier->id)>
                                                    {{ app()->getLocale() === 'ar' ? $quartier->name_ar : $quartier->name_fr }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('quartier_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <p id="location-area" class="mt-3 hidden text-sm text-gray-500"></p>

                            <div class="mt-4">
                                <div id="map" class="w-full h-96 rounded-lg border-2 border-gray-300 shadow-sm"></div>
                                <div class="mt-3 flex flex-col sm:flex-row gap-3">
                                    <button type="button" id="use-location-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ __('Use My Location') }}
                                    </button>
                                    <div class="flex gap-3 flex-1">
                                        <div class="flex-1">
                                            <x-input-label for="latitude" :value="__('Latitude')" class="text-sm font-medium" />
                                            <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-2 px-3 text-sm"
                                                placeholder="33.9716" :value="old('latitude')" readonly />
                                            @error('latitude')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex-1">
                                            <x-input-label for="longitude" :value="__('Longitude')" class="text-sm font-medium" />
                                            <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-2 px-3 text-sm"
                                                placeholder="-6.8498" :value="old('longitude')" readonly />
                                            @error('longitude')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">{{ __('Click on the map to set the exact location, or use your current location.') }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-semibold">← {{ __('Back') }}</a>
                            <x-primary-button class="px-8 py-3 text-lg">{{ __('Submit Report') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- exif-js: reads GPS tags embedded in the uploaded photo, when present, to pre-place the map pin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js"></script>

    <script>
        window.reportConfig = {
            resolveUrl: "{{ route('location.resolve') }}",
            translations: {
                imageTypes: "{{ __('validation.image_types') }}",
                imageSize: "{{ __('validation.image_size') }}",
                gettingLocation: "{{ __('Getting location...') }}",
                useMyLocation: "{{ __('Use My Location') }}",
                geolocationError: "{{ __('Unable to get your location. Please click on the map to set the location manually.') }}",
                geolocationDenied: "{{ __('Location access is blocked. Allow it in your browser, or tap the map to place the pin yourself.') }}",
                geolocationNotSupported: "{{ __('Geolocation is not supported by this browser.') }}",
                locatedFromPhoto: "{{ __('Location read from the photo. Drag the pin to fine-tune.') }}",
                locatedFromDevice: "{{ __('Using your current location. Drag the pin to fine-tune.') }}",
                pinnedManually: "{{ __('Pin placed manually.') }}",
                photoNoGps: "{{ __('This photo has no GPS data (common for shared or screenshot images).') }}",
                photoNoGpsPng: "{{ __('PNG images cannot carry GPS data.') }}",
                tryingDevice: "{{ __('Trying your device location instead...') }}",
                keepingCurrentPin: "{{ __('Keeping the pin already set.') }}",
                checkingArea: "{{ __('Checking area...') }}",
                areaUnknown: "{{ __('Area could not be determined — drag the pin closer to the street.') }}",
                cityLevelOnly: "{{ __('city level only, no district data for this area yet') }}",
                outsideArea: "{{ __('This location is outside our coverage area. Please set it manually.') }}",
                notAvailable: "{{ __('Not available') }}",
                correctedManually: "{{ __('Pin corrected. Location updated.') }}",
                selectDistrict: "{{ __('-- Select a district --') }}",
                selectQuartier: "{{ __('-- Select a quartier --') }}"
            }
        };
    </script>
    <script src="{{ asset('js/reports/create.js') }}"></script>
</x-app-layout>
