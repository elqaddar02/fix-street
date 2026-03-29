@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion des utilisateurs</h1>
        <p class="text-gray-600">Recherche et gestion des comptes</p>
    </div>

    <form method="GET" class="flex gap-2 max-w-md">
        <input name="search" value="{{ $search ?? '' }}" class="w-full border-2 border-gray-400 rounded-lg px-3 py-2" placeholder="Chercher par nom ou email" />
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Rechercher</button>
    </form>

    <div class="overflow-auto rounded-xl border border-red-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-600">Nom</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-600">Email</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-600">Actif</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->active ? 'Oui' : 'Non' }}</td>
                    <td class="px-4 py-3 space-x-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:underline">Voir</a>
                        <form action="{{ route('admin.users.updateStatus', $user) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}" />
                            <button type="submit" class="text-yellow-600 hover:underline">{{ $user->active ? 'Désactiver' : 'Activer' }}</button>
                        </form>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Confirmer suppression ?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection
