@extends('admin.layouts.app')

@section('title', 'Edit City')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Modifier la ville</h1>
        <p class="text-gray-600">Mettre à jour le nom ou l'état activer/désactiver</p>
    </div>

    <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="grid gap-3 max-w-lg">
        @csrf
        @method('PATCH')

        <div>
            <label class="text-sm font-semibold">Nom</label>
            <input name="name" value="{{ old('name', $city->name) }}" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2" required />
        </div>

        <div>
            <label class="text-sm font-semibold">Actif</label>
            <select name="active" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2" required>
                <option value="1" {{ old('active', $city->active) ? 'selected' : '' }}>Oui</option>
                <option value="0" {{ ! old('active', $city->active) ? 'selected' : '' }}>Non</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white">Enregistrer</button>
            <a href="{{ route('admin.cities.index') }}" class="rounded-lg bg-gray-100 px-4 py-2">Annuler</a>
        </div>
    </form>
</div>
@endsection