@extends('admin.layouts.app')

@section('title', 'Edit Quartier')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier le quartier</h1>
        <p class="text-gray-600">Mettre à jour le nom, la ville ou l'état activer/désactiver</p>
    </div>

    <form method="POST" action="{{ route('admin.quartiers.update', $quartier) }}" class="grid gap-3 max-w-lg">
        @csrf
        @method('PATCH')

        <div>
            <label class="text-sm font-semibold">Ville</label>
            <select name="city_id" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2" required>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $quartier->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-semibold">Nom du quartier</label>
            <input name="name" value="{{ old('name', $quartier->name) }}" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2" required />
        </div>

        <div>
            <label class="text-sm font-semibold">Actif</label>
            <select name="active" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2" required>
                <option value="1" {{ old('active', $quartier->active) ? 'selected' : '' }}>Oui</option>
                <option value="0" {{ ! old('active', $quartier->active) ? 'selected' : '' }}>Non</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white">Enregistrer</button>
            <a href="{{ route('admin.quartiers.index') }}" class="rounded-lg bg-gray-100 px-4 py-2">Annuler</a>
        </div>
    </form>
</div>
@endsection