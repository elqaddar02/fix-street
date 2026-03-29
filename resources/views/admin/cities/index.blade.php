@extends('admin.layouts.app')

@section('title', 'Cities')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion des villes</h1>
        <p class="text-gray-600">Ajouter, modifier et activer/désactiver les villes</p>
    </div>

    <form method="POST" action="{{ route('admin.cities.store') }}" class="grid gap-3 md:grid-cols-3 items-end">
        @csrf
        <div><label class="text-sm font-semibold">Nom</label><input name="name" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2"/></div>
        <div><label class="text-sm font-semibold">Actif</label><select name="active" class="mt-1 w-full rounded-lg border-2 border-gray-400 px-3 py-2"><option value="1">Oui</option><option value="0">Non</option></select></div>
        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white">Ajouter</button>
    </form>

    <div class="overflow-auto rounded-xl border border-red-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Nom</th>
                    <th class="px-4 py-2 text-left text-gray-600">Actif</th>
                    <th class="px-4 py-2 text-left text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cities as $city)
                    <tr>
                        <td class="px-4 py-2">{{ $city->name }}</td>
                        <td class="px-4 py-2">{{ $city->active ? 'Oui' : 'Non' }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('admin.cities.edit', $city) }}" class="text-blue-600 hover:underline">Modifier</a>
                            <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette ville ?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>{{ $cities->links() }}</div>
</div>
@endsection