@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Controle central des opérations</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-red-600 uppercase font-semibold">Total utilisateurs</p>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-red-600 uppercase font-semibold">Total signalements</p>
            <p class="text-3xl font-bold">{{ $totalReports }}</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-red-600 uppercase font-semibold">Total villes</p>
            <p class="text-3xl font-bold">{{ $totalCities }}</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-red-600 uppercase font-semibold">Statut des signalements</p>
            <div class="mt-3 space-y-2 text-sm">
                <p>Ouvert: {{ $statusCounts['OPEN'] ?? 0 }}</p>
                <p>En cours: {{ $statusCounts['IN_PROGRESS'] ?? 0 }}</p>
                <p>Résolu: {{ $statusCounts['RESOLVED'] ?? 0 }}</p>
                <p>Rejeté: {{ $statusCounts['REJECTED'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900">Derniers signalements</h2>
            <ul class="mt-4 space-y-3">
                @forelse($latestReports as $report)
                    <li class="border rounded-lg p-3 bg-gray-50">
                        <a href="{{ route('admin.reports.show', $report) }}" class="font-semibold text-red-700">{{ $report->title }}</a>
                        <p class="text-xs text-gray-500">{{ $report->city->name ?? 'N/A' }} - {{ $report->status }}</p>
                    </li>
                @empty
                    <li class="text-gray-500">Aucun signalement récent.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-red-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900">Derniers utilisateurs</h2>
            <ul class="mt-4 space-y-3">
                @forelse($latestUsers as $user)
                    <li class="border rounded-lg p-3 bg-gray-50">
                        <p class="font-semibold">{{ $user->name }} ({{ $user->email }})</p>
                        <p class="text-xs text-gray-500">Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
                    </li>
                @empty
                    <li class="text-gray-500">Aucun utilisateur récent.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
