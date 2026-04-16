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
                        <p class="text-gray-600 mt-1">{{ __('Share details about the street maintenance problem you\'ve found.') }}</p>
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

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Report Title')" class="text-lg font-semibold" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                placeholder="{{ __('Brief description of the problem') }}" :value="old('title')" required />
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Detailed Description')" class="text-lg font-semibold" />
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                placeholder="{{ __('Provide detailed information about the problem...') }}" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category, City and District -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                            <div>
                                <x-input-label for="city_id" :value="__('City')" class="text-lg font-semibold" />
                                <select id="city_id" name="city_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">{{ __('-- Select a city --') }}</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id') == $city->id) @disabled(!$city->active)>
                                            {{ $city->display_name }} @if(!$city->active) (Bientôt disponible) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="district_id" :value="__('District')" class="text-lg font-semibold" />
                                <select id="district_id" name="district_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">{{ __('-- Select a district --') }}</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" data-lat="{{ $district->lat ?? 33.5731 }}" data-lng="{{ $district->lng ?? -7.5898 }}" @selected(old('district_id') == $district->id)>
                                            {{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        <!-- Location Map -->
                        <div>
                            <x-input-label :value="__('Location')" class="text-lg font-semibold" />
                            <div class="mt-2">
                                <div id="map" class="w-full h-64 rounded-lg border-2 border-gray-300 shadow-sm"></div>
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
                        <div>
                            <x-input-label for="image" :value="__('Upload Photo (optional but recommended)')" class="text-lg font-semibold" />
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition hover:border-red-500 hover:bg-red-50 cursor-pointer" id="image-upload-area">
                                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class=\"text-gray-700 font-semibold\">{{ __('Click to upload or drag and drop') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('JPG, JPEG, PNG up to 2MB') }}</p>
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

    <script>
        const imageInput = document.getElementById('image');
        const imageUploadArea = document.getElementById('image-upload-area');
        const imagePreview = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');

        // Click to upload (only if the click did not already hit the file input)
        imageUploadArea.addEventListener('click', (event) => {
            if (event.target !== imageInput) {
                imageInput.click();
            }
        });

        // Drag and drop
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.classList.add('border-red-500', 'bg-red-50');
        });

        imageUploadArea.addEventListener('dragleave', () => {
            imageUploadArea.classList.remove('border-red-500', 'bg-red-50');
        });

        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadArea.classList.remove('border-red-500', 'bg-red-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                showImagePreview();
            }
        });

        // Show preview when file is selected
        imageInput.addEventListener('change', showImagePreview);

        function showImagePreview() {
            if (imageInput.files && imageInput.files[0]) {
                const file = imageInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                // Client-side validation
                if (!allowedTypes.includes(file.type)) {
                    showImageAlert('{{ __("validation.image_types") }}');
                    imageInput.value = '';
                    imagePreview.classList.add('hidden');
                    return;
                }
                
                if (file.size > maxSize) {
                    showImageAlert('{{ __("validation.image_size") }}');
                    imageInput.value = '';
                    imagePreview.classList.add('hidden');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        const imageAlert = document.getElementById('image-alert');
        const imageAlertText = document.getElementById('image-alert-text');
        let imageAlertTimeout;

        function showImageAlert(message) {
            if (!imageAlert || !imageAlertText) return;
            imageAlertText.textContent = message;
            imageAlert.classList.remove('hidden');
            clearTimeout(imageAlertTimeout);
            imageAlertTimeout = setTimeout(() => {
                imageAlert.classList.add('hidden');
            }, 4000);
        }

        // District and map logic
        const citySelect = document.getElementById('city_id');
        const districtSelect = document.getElementById('district_id');
        
        // Geolocation data for districts (stored in data attributes)
        const districtCoordinates = {};
        Array.from(districtSelect.options).forEach(option => {
            if (option.value) {
                districtCoordinates[option.value] = {
                    lat: parseFloat(option.dataset.lat) || 33.5731,
                    lng: parseFloat(option.dataset.lng) || -7.5898,
                    name: option.textContent
                };
            }
        });

        // Update map when district changes
        districtSelect.addEventListener('change', function() {
            if (this.value && districtCoordinates[this.value]) {
                const coords = districtCoordinates[this.value];
                updateMapLocation(coords.lat, coords.lng);
            }
        });

        // Update map when city changes
        citySelect.addEventListener('change', function() {
            if (this.value) {
                // Reset district and map to default Salé center
                districtSelect.value = '';
                districtSelect.disabled = false;
                updateMapLocation(33.5731, -7.5898);
            } else {
                districtSelect.disabled = true;
                districtSelect.value = '';
            }
        });

        function updateMapLocation(lat, lng) {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            
            // Update map if it's initialized
            if (typeof marker !== 'undefined' && marker) {
                map.removeLayer(marker);
            }
            if (typeof map !== 'undefined') {
                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 15);
            }
        }

        // Initialize on page load if a district was previously selected
        if (districtSelect.value && districtCoordinates[districtSelect.value]) {
            const coords = districtCoordinates[districtSelect.value];
            document.getElementById('latitude').value = coords.lat.toFixed(6);
            document.getElementById('longitude').value = coords.lng.toFixed(6);
        }

        // Initialize map
        let map;
        let marker;

        function initMap() {
            // Fix Leaflet icon paths for Vite build
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: '/images/marker-icon-2x.png',
                iconUrl: '/images/marker-icon.png',
                shadowUrl: '/images/marker-shadow.png',
            });

            // Default location (Morocco center)
            const defaultLat = 31.7917;
            const defaultLng = -7.0926;
            const zoom = 6;

            // Create map
            map = L.map('map').setView([defaultLat, defaultLng], zoom);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Add click event to set location
            map.on('click', function(e) {
                setLocation(e.latlng.lat, e.latlng.lng);
            });

            // Set initial location if values exist
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            if (latInput.value && lngInput.value) {
                setLocation(parseFloat(latInput.value), parseFloat(lngInput.value));
            }
        }

        function setLocation(lat, lng) {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            // Remove existing marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 15);
        }

        // Use current location
        document.getElementById('use-location-btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                this.disabled = true;
                this.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>{{ __("Getting location...") }}';

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        setLocation(lat, lng);
                        document.getElementById('use-location-btn').disabled = false;
                        document.getElementById('use-location-btn').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ __("Use My Location") }}';
                    },
                    function(error) {
                        console.error('Geolocation error:', error);
                        alert('{{ __("Unable to get your location. Please click on the map to set the location manually.") }}');
                        document.getElementById('use-location-btn').disabled = false;
                        document.getElementById('use-location-btn').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ __("Use My Location") }}';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000
                    }
                );
            } else {
                alert('{{ __("Geolocation is not supported by this browser.") }}');
            }
        });

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</x-app-layout>
