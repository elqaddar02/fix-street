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
            {{-- Same inline-save behaviour as the list: change it, it saves, the toast offers an undo. --}}
            <div class="flex items-center gap-3">
                <label for="report-status" class="text-sm font-medium text-slate-600">Statut</label>
                <select id="report-status"
                        name="status"
                        data-status-select
                        data-action="{{ route('admin.reports.updateStatus', $report) }}"
                        @class([
                            'rounded-lg border px-3 py-2 text-sm font-medium transition-colors focus:ring-1 disabled:opacity-50',
                            'border-amber-200 bg-amber-50 text-amber-800 focus:border-amber-500 focus:ring-amber-500' => $report->status === 'OPEN',
                            'border-sky-200 bg-sky-50 text-sky-800 focus:border-sky-500 focus:ring-sky-500' => $report->status === 'IN_PROGRESS',
                            'border-emerald-200 bg-emerald-50 text-emerald-800 focus:border-emerald-500 focus:ring-emerald-500' => $report->status === 'RESOLVED',
                            'border-rose-200 bg-rose-50 text-rose-800 focus:border-rose-500 focus:ring-rose-500' => $report->status === 'REJECTED',
                        ])>
                    <option value="OPEN" @selected($report->status === 'OPEN')>Ouvert</option>
                    <option value="IN_PROGRESS" @selected($report->status === 'IN_PROGRESS')>En cours</option>
                    <option value="RESOLVED" @selected($report->status === 'RESOLVED')>Résolu</option>
                    <option value="REJECTED" @selected($report->status === 'REJECTED')>Rejeté</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.reports.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm hover:bg-gray-200">Retour</a>
        </div>
    </div>
</div>
@endsection