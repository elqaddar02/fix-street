<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Report') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Edit Report') }}</h3>
                        <p class="text-gray-600 mt-1">{{ __('Update the details of your street maintenance report.') }}</p>
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

                    <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PATCH')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Report Title')" class="text-lg font-semibold" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4"
                                placeholder="{{ __('Brief description of the problem') }}" value="{{ old('title', $report->title) }}" required />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Detailed Description')" class="text-lg font-semibold" />
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4"
                                placeholder="{{ __('Provide detailed information about the problem...') }}" required>{{ old('description', $report->description) }}</textarea>
                        </div>

                        <!-- Category, City and District -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" class="text-lg font-semibold" />
                                <select id="category_id" name="category_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">{{ __('-- Select a category --') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $report->category_id) == $category->id)>
                                            {{ $category->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('City')" class="text-lg font-semibold" />
                                <select id="city_id" name="city_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">{{ __('-- Select a city --') }}</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id', $report->city_id) == $city->id) @disabled(!$city->active)>
                                            {{ $city->display_name }} @if(!$city->active) (Bientôt disponible) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="district_id" :value="__('District')" class="text-lg font-semibold" />
                                <select id="district_id" name="district_id" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4">
                                    <option value="">{{ __('-- Select a district --') }}</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" data-city="{{ $district->city_id }}" @selected(old('district_id', $report->district_id) == $district->id)>
                                            {{ app()->getLocale() === 'ar' ? $district->name_ar : $district->name_fr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude (optional)')" class="text-lg font-semibold" />
                                <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4"
                                    placeholder="ex: 40.7128" value="{{ old('latitude', $report->latitude) }}" />
                                <p class="text-xs text-gray-500 mt-1">Coordonnée GPS - laisser vide si inconnu</p>
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude (optional)')" class="text-lg font-semibold" />
                                <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" class="mt-2 block w-full border-2 border-gray-400 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4"
                                    placeholder="ex: -74.0060" value="{{ old('longitude', $report->longitude) }}" />
                                <p class="text-xs text-gray-500 mt-1">Coordonnée GPS - laisser vide si inconnu</p>
                            </div>
                        </div>

                        <!-- Current Image -->
                        @if($report->image)
                            <div>
                                <x-input-label :value="__('Current Photo')" class="text-lg font-semibold" />
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $report->image) }}" alt="Current report image" class="max-h-48 rounded-lg shadow-md">
                                </div>
                            </div>
                        @endif

                        <!-- Image Upload -->
                        <div>
                            <x-input-label for="image" :value="__('Upload New Photo (optional)')" class="text-lg font-semibold" />
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition hover:border-red-500 hover:bg-red-50 cursor-pointer" id="image-upload-area">
                                    <input id="image" name="image" type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold">{{ __('Click to upload or drag and drop') }}</p>
                                        <p class="text-sm text-gray-500">{{ __('PNG, JPG, GIF up to 2MB') }}</p>
                                    </div>
                                </div>
                                <div id="image-preview" class="mt-4 hidden">
                                    <img id="preview-image" src="" alt="Preview" class="max-h-48 rounded-lg shadow-md mx-auto">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-semibold">← {{ __('Back') }}</a>
                            <x-primary-button class="px-8 py-3 text-lg">{{ __('Update Report') }}</x-primary-button>
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

        // Click to upload
        imageUploadArea.addEventListener('click', () => imageInput.click());

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
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(imageInput.files[0]);
            }
        }

        // District filtering based on city selection
        const citySelect = document.getElementById('city_id');
        const districtSelect = document.getElementById('district_id');
        const allDistrictOptions = Array.from(districtSelect.options);

        citySelect.addEventListener('change', function() {
            const selectedCityId = this.value;
            
            // Reset district select
            districtSelect.innerHTML = '<option value="">{{ __("-- Select a district --") }}</option>';
            
            if (selectedCityId) {
                // Filter and show districts for the selected city
                const filteredDistricts = allDistrictOptions.filter(option => 
                    option.value === '' || option.dataset.city == selectedCityId
                );
                
                if (filteredDistricts.length > 1) {
                    filteredDistricts.forEach(option => {
                        if (option.value !== '') {
                            districtSelect.appendChild(option.cloneNode(true));
                        }
                    });
                    districtSelect.disabled = false;
                } else {
                    districtSelect.disabled = true;
                    districtSelect.innerHTML = '<option value="">{{ __("-- No districts available --") }}</option>';
                }
            } else {
                districtSelect.disabled = true;
                districtSelect.innerHTML = '<option value="">{{ __("-- Select a city first --") }}</option>';
            }
        });

        // Trigger city change on page load to show districts for current city
        if (citySelect.value) {
            citySelect.dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>