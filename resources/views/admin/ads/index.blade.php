@extends('admin.layouts.app')

@section('title', 'Publicités')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Publicités</h1>
            <p class="text-gray-600">Gérer les publicités par emplacement</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvelle publicité
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Publicité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Emplacement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fenêtre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Impressions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Clics</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ads as $ad)
                        @php($badge = adStatusBadge($ad))
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($ad->image)
                                        <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="h-10 w-16 rounded border border-gray-200 object-cover">
                                    @else
                                        <div class="h-10 w-16 rounded border border-dashed border-gray-300 bg-gray-50"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $ad->title }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($ad->provider) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $ad->placement->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $ad->starts_at?->format('d/m/Y H:i') ?? '—' }}
                                →
                                {{ $ad->ends_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $ad->impressions_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $ad->clicks_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <form action="{{ route('admin.ads.updateStatus', $ad) }}" method="POST" class="inline-flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $ad->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-600 hover:text-gray-900">
                                        {{ $ad->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.ads.edit', $ad) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Éditer
                                </a>
                                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Êtes-vous sûr?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 hover:text-red-700">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <p>Aucune publicité créée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($ads->hasPages())
        <div class="mt-6">
            {{ $ads->links() }}
        </div>
    @endif
</div>
@endsection
