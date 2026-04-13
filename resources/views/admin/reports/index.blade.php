@extends('admin.layouts.app')

@section('title', 'Manage Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion des signalements</h1>
        <p class="text-gray-600">Filtrer et mettre à jour le statut des signalements</p>
    </div>

    <div class="flex gap-2">
        @php
            $currentStatus = request('status');
            $statusLabel = 'Tous';
            $statusClass = 'border-slate-400 bg-white text-slate-900';

            if ($currentStatus === 'OPEN') {
                $statusLabel = 'Ouvert';
                $statusClass = 'border-amber-400 bg-amber-50 text-amber-900';
            } elseif ($currentStatus === 'IN_PROGRESS') {
                $statusLabel = 'En cours';
                $statusClass = 'border-sky-400 bg-sky-50 text-sky-900';
            } elseif ($currentStatus === 'RESOLVED') {
                $statusLabel = 'Résolu';
                $statusClass = 'border-emerald-400 bg-emerald-50 text-emerald-900';
            } elseif ($currentStatus === 'REJECTED') {
                $statusLabel = 'Rejeté';
                $statusClass = 'border-rose-400 bg-rose-50 text-rose-900';
            }
        @endphp

        <form method="GET" class="flex gap-2 items-center flex-wrap">
            <div class="status-field flex items-center gap-3">
                <span class="status-badge inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $currentStatus === 'OPEN' ? 'text-amber-800 bg-amber-100 border-amber-300' : ($currentStatus === 'IN_PROGRESS' ? 'text-sky-800 bg-sky-100 border-sky-300' : ($currentStatus === 'RESOLVED' ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : ($currentStatus === 'REJECTED' ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-slate-700 bg-slate-100 border-slate-300'))) }}">
                    {{ $statusLabel }}
                </span>
                <select name="status" class="status-select rounded-xl border-2 px-3 py-2 {{ $statusClass }} bg-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 transition-colors">
                    <option value="" {{ $currentStatus === '' ? 'selected' : '' }}>Tous</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Ouvert</option>
                    <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>En cours</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>Résolu</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>

            <select name="sort" class="rounded-xl border-2 border-slate-400 px-3 py-2 bg-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                <option value="latest" @selected($sortBy === 'latest')>Récent</option>
                <option value="oldest" @selected($sortBy === 'oldest')>Ancien</option>
                <option value="mostLiked" @selected($sortBy === 'mostLiked')>Plus Important</option>
            </select>

            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Appliquer</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.reports.bulkUpdateStatus') }}" id="bulk-action-form" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <label for="bulk-status" class="text-sm font-medium text-gray-700">Changer le statut :</label>
                <div class="status-field flex items-center gap-3">
                    <span class="status-badge inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold text-slate-700 bg-slate-100 border-slate-300">
                        Statut
                    </span>
                    <select id="bulk-status" name="status" class="status-select rounded-xl border-2 px-3 py-2 bg-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 transition-colors">
                        <option value="">Sélectionner...</option>
                        <option value="OPEN">Ouvert</option>
                        <option value="IN_PROGRESS">En cours</option>
                        <option value="RESOLVED">Résolu</option>
                        <option value="REJECTED">Rejeté</option>
                    </select>
                </div>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Appliquer</button>
            </div>
            <p class="text-sm text-gray-500">Cochez les signalements à mettre à jour en lot.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input id="select-all" type="checkbox" class="h-4 w-4 text-red-600 rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="report_ids[]" value="{{ $report->id }}" class="h-4 w-4 text-red-600 rounded border-gray-300">
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $report->city->display_name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="status-field flex items-center gap-3">
                                <span class="status-badge inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold
                                    {{ $report->status === 'OPEN' ? 'text-amber-800 bg-amber-100 border-amber-300' : ($report->status === 'IN_PROGRESS' ? 'text-sky-800 bg-sky-100 border-sky-300' : ($report->status === 'RESOLVED' ? 'text-emerald-800 bg-emerald-100 border-emerald-300' : ($report->status === 'REJECTED' ? 'text-rose-800 bg-rose-100 border-rose-300' : 'text-slate-700 bg-slate-100 border-slate-300'))) }}">
                                    {{ $report->status === 'OPEN' ? 'Ouvert' : ($report->status === 'IN_PROGRESS' ? 'En cours' : ($report->status === 'RESOLVED' ? 'Résolu' : ($report->status === 'REJECTED' ? 'Rejeté' : 'Non défini'))) }}
                                </span>
                                <select name="status" data-current="{{ $report->status }}" data-confirm-change="true" data-confirm-message="Changer le statut du signalement ?" class="status-select rounded-xl border-2 px-3 py-2 bg-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 transition-colors {{ $report->status === 'OPEN' ? 'border-amber-400 bg-amber-50 text-amber-900' : ($report->status === 'IN_PROGRESS' ? 'border-sky-400 bg-sky-50 text-sky-900' : ($report->status === 'RESOLVED' ? 'border-emerald-400 bg-emerald-50 text-emerald-900' : ($report->status === 'REJECTED' ? 'border-rose-400 bg-rose-50 text-rose-900' : 'border-slate-400 bg-slate-50 text-slate-900'))) }}">
                                    <option value="OPEN" {{ $report->status === 'OPEN' ? 'selected' : '' }}>Ouvert</option>
                                    <option value="IN_PROGRESS" {{ $report->status === 'IN_PROGRESS' ? 'selected' : '' }}>En cours</option>
                                    <option value="RESOLVED" {{ $report->status === 'RESOLVED' ? 'selected' : '' }}>Résolu</option>
                                    <option value="REJECTED" {{ $report->status === 'REJECTED' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="inline-flex items-center gap-2 {{ $report->likes_count > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600' }} px-3 py-1 rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            <span class="text-xs font-semibold">{{ $report->likes_count ?? 0 }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.reports.show', $report) }}"
                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Détails
                        </a>
                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Confirmer suppression ?')" class="inline-block">
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
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center">
                        <div class="text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Aucun signalement trouvé.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </form>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function (event) {
            document.querySelectorAll('input[name="report_ids[]"]').forEach(function (checkbox) {
                checkbox.checked = event.target.checked;
            });
        });

        document.getElementById('bulk-action-form')?.addEventListener('submit', function (event) {
            const selectedCount = document.querySelectorAll('input[name="report_ids[]"]:checked').length;
            const selectedStatus = document.getElementById('bulk-status').value;

            if (!selectedCount) {
                alert('Veuillez sélectionner au moins un signalement.');
                event.preventDefault();
                return;
            }

            if (!selectedStatus) {
                alert('Veuillez choisir un statut à appliquer.');
                event.preventDefault();
                return;
            }

            if (!confirm('Appliquer le statut sélectionné aux signalements cochés ?')) {
                event.preventDefault();
            }
        });
    </script>

    <div>{{ $reports->links() }}</div>
</div>
@endsection
