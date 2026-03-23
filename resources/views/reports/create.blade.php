<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Report') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">
                            <p class="font-semibold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('City')" />
                                <select id="city_id" name="city_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Select city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude (optional)')" />
                                <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('latitude')" />
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude (optional)')" />
                                <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" class="mt-1 block w-full" :value="old('longitude')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Image (optional)')" />
                            <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>{{ __('Submit Report') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
