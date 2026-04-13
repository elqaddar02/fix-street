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
                <p>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                        @if($report->status === 'OPEN') bg-amber-100 text-amber-800 border border-amber-300
                        @elseif($report->status === 'IN_PROGRESS') bg-sky-100 text-sky-800 border border-sky-300
                        @elseif($report->status === 'RESOLVED') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($report->status === 'REJECTED') bg-rose-100 text-rose-800 border border-rose-300
                        @else bg-slate-100 text-slate-700 border border-slate-300 @endif">
                        @if($report->status === 'OPEN') Ouvert
                        @elseif($report->status === 'IN_PROGRESS') En cours
                        @elseif($report->status === 'RESOLVED') Résolu
                        @elseif($report->status === 'REJECTED') Rejeté
                        @else Non défini
                        @endif
                    </span>
                </p>
            </div>
            <div>
                <p class="font-semibold">Catégorie:</p>
                <p>{{ $report->category->display_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="font-semibold">Ville:</p>
                <p>{{ $report->city->name ?? 'N/A' }}</p>
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
            <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-center" onsubmit="return confirm('Changer le statut du signalement ?');">
                @csrf
                @method('PATCH')
                <div class="status-field flex items-center gap-3">
                    <span class="status-badge inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $report->status === 'OPEN' ? 'text-amber-800 bg-amber-100 border-amber-300' : ($report->status === 'IN_PROGRESS' ? 'text-sky-800 bg-sky-100 border-sky-300' : ($report->status === 'RESOLVED' ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : 'text-rose-800 bg-rose-100 border-rose-300')) }}">
                        {{ $report->status === 'OPEN' ? 'Ouvert' : ($report->status === 'IN_PROGRESS' ? 'En cours' : ($report->status === 'RESOLVED' ? 'Résolu' : 'Rejeté')) }}
                    </span>
                    <select name="status" class="status-select rounded-xl border-2 px-3 py-2 {{ $report->status === 'OPEN' ? 'border-amber-400 bg-amber-50 text-amber-900' : ($report->status === 'IN_PROGRESS' ? 'border-sky-400 bg-sky-50 text-sky-900' : ($report->status === 'RESOLVED' ? 'border-emerald-400 bg-emerald-50 text-emerald-900' : 'border-rose-400 bg-rose-50 text-rose-900')) }} text-sm font-medium shadow-sm focus:outline-none focus:ring-2 transition-colors">
                        <option value="OPEN" {{ $report->status === 'OPEN' ? 'selected' : '' }}>Ouvert</option>
                        <option value="IN_PROGRESS" {{ $report->status === 'IN_PROGRESS' ? 'selected' : '' }}>En cours</option>
                        <option value="RESOLVED" {{ $report->status === 'RESOLVED' ? 'selected' : '' }}>Résolu</option>
                        <option value="REJECTED" {{ $report->status === 'REJECTED' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Mettre à jour</button>
            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.reports.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm hover:bg-gray-200">Retour</a>
        </div>
    </div>
</div>
@endsection