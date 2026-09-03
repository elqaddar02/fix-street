@extends('admin.layouts.app')

@section('title', 'Modifier la publicité')

@section('content')
<div class="space-y-8 max-w-2xl">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier la publicité</h1>
        <p class="text-gray-600">{{ $ad->title }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            @include('admin.ads._form', ['ad' => $ad, 'placements' => $placements])

            <!-- Actions -->
            <div class="flex items-center space-x-4 pt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Enregistrer
                </button>
                <a href="{{ route('admin.ads.index') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
