@extends('admin.layouts.app')

@section('title', 'Report Detail')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Détails du signalement</h1>
        <p class="text-gray-600">Informations complètes</p>
    </div>

    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-800">{{ $report->title }}</h2>
        <p class="text-gray-600 mb-4">{{ $report->description }}</p>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="font-semibold">Statut:</p>
                <p>{{ $report->status }}</p>
            </div>
            <div>
                <p class="font-semibold">Catégorie:</p>
                <p>{{ $report->category->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="font-semibold">Ville:</p>
                <p>{{ $report->city->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="font-semibold">Quartier:</p>
                <p>{{ $report->quartier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="font-semibold">Utilisateur:</p>
                <p>{{ $report->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="font-semibold">Créé le:</p>
                <p>{{ $report->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-6">
            <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="rounded-lg border-2 border-gray-400 px-3 py-2">
                    <option value="OPEN" {{ $report->status === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="IN_PROGRESS" {{ $report->status === 'IN_PROGRESS' ? 'selected' : '' }}>IN_PROGRESS</option>
                    <option value="RESOLVED" {{ $report->status === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                    <option value="REJECTED" {{ $report->status === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                </select>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Mettre à jour</button>
            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.reports.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm hover:bg-gray-200">Retour</a>
        </div>
    </div>
</div>
@endsection