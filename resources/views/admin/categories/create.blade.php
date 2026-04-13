@extends('admin.layouts.app')

@section('title', 'Créer une catégorie')

@section('content')
<div class="space-y-8 max-w-2xl">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Créer une catégorie</h1>
        <p class="text-gray-600">Ajouter une nouvelle catégorie de signalements</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom (Français)</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name Arabic -->
            <div>
                <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">Nom (Arabe)</label>
                <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name_ar') border-red-500 @enderror" dir="rtl">
                @error('name_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4 pt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Créer la catégorie
                </button>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
