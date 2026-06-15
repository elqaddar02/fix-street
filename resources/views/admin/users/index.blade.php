
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

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Liste des utilisateurs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.users.updateStatus', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="status-field inline-flex items-center gap-3">
                                        <span class="status-badge inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $user->active ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : 'text-slate-700 bg-slate-100 border-slate-300' }}">
                                            {{ $user->active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        <select name="active" data-current="{{ $user->active ? 1 : 0 }}" data-confirm-change="true" data-confirm-message="Changer le statut de l'utilisateur ?" class="status-select rounded-xl border-2 px-3 py-2 {{ $user->active ? 'border-emerald-400 bg-emerald-50 text-emerald-900' : 'border-slate-400 bg-slate-50 text-slate-900' }} text-sm font-medium shadow-sm focus:outline-none focus:ring-2 transition-colors">
                                            <option value="1" {{ $user->active ? 'selected' : '' }}>Actif</option>
                                            <option value="0" {{ !$user->active ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Voir
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Confirmer suppression ?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection
