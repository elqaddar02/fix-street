@extends('admin.layouts.app')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Profil utilisateur</h1>
        <p class="text-gray-600">Détails du compte</p>
    </div>

    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
        <dl class="grid gap-3 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase text-gray-500">Nom</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500">Email</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500">Administrateur</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $user->is_admin ? 'Oui' : 'Non' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500">Actif</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $user->active ? 'Oui' : 'Non' }}</dd>
            </div>
        </dl>

        <div class="mt-6 space-x-2">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm">Retour</a>
        </div>
    </div>
</div>
@endsection
