@extends('admin.layouts.app')

@section('title', 'Manage Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion des signalements</h1>
        <p class="text-gray-600">Filtrer et mettre à jour le statut des signalements</p>
    </div>

    <div class="flex gap-2">
        <form method="GET" class="flex gap-2">
            <select name="status" class="rounded-lg border-2 border-gray-400 px-3 py-2">
                <option value="">Tous</option>
                <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>IN_PROGRESS</option>
                <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
            </select>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Appliquer</button>
        </form>
    </div>

    <div class="overflow-auto rounded-xl border border-red-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-600">Titre</th>
                    <th class="px-4 py-2 text-left text-gray-600">Ville</th>
                    <th class="px-4 py-2 text-left text-gray-600">Quartier</th>
                    <th class="px-4 py-2 text-left text-gray-600">Statut</th>
                    <th class="px-4 py-2 text-left text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($reports as $report)
                <tr>
                    <td class="px-4 py-2 font-medium text-red-700">{{ $report->title }}</td>
                    <td class="px-4 py-2">{{ $report->city->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $report->quartier->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $report->status }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-600 hover:underline">Détails</a>
                        <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Confirmer suppression ?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucun signalement trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $reports->links() }}</div>
</div>
@endsection
