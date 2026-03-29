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
                        <h3 class="text-2xl font-bold text-gray-900">Report a Street Issue</h3>
                        <p class="text-gray-600 mt-1">Share details about the street maintenance problem you've found.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border-l-4 border-red-600 bg-red-50 p-4 text-red-700">
                            <p class="font-semibold mb-2">Please fix the following errors:</p>
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
                            <x-text-input id="title" name="title" type="text" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                placeholder="Brief description of the issue" :value="old('title')" required />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Detailed Description')" class="text-lg font-semibold" />
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                placeholder="Provide detailed information about the issue..." required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Category and City -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" class="text-lg font-semibold" />
                                <select id="category_id" name="category_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">-- Select category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('City')" class="text-lg font-semibold" />
                                <select id="city_id" name="city_id" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" required>
                                    <option value="">-- Select city --</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude (optional)')" class="text-lg font-semibold" />
                                <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                    placeholder="e.g., 40.7128" :value="old('latitude')" />
                                <p class="text-xs text-gray-500 mt-1">GPS coordinate - leave blank if unsure</p>
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude (optional)')" class="text-lg font-semibold" />
                                <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 py-3 px-4" 
                                    placeholder="e.g., -74.0060" :value="old('longitude')" />
                                <p class="text-xs text-gray-500 mt-1">GPS coordinate - leave blank if unsure</p>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <x-input-label for="image" :value="__('Upload Photo (optional but recommended)')" class="text-lg font-semibold" />
                            <div class="mt-2">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition hover:border-red-500 hover:bg-red-50 cursor-pointer" id="image-upload-area">
                                    <input id="image" name="image" type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                                <div id="image-preview" class="mt-4 hidden">
                                    <img id="preview-image" src="" alt="Preview" class="max-h-48 rounded-lg shadow-md mx-auto">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-semibold">← Back</a>
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
    </script>
</x-app-layout>
