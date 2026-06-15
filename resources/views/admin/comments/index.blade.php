@extends('admin.layouts.app')

@section('title', 'Modération des commentaires')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Commentaires</h1>
            <p class="text-gray-600">Modérer et approuver les commentaires des utilisateurs</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 border-b border-gray-200">
        <a href="{{ route('admin.comments.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ !request('status') ? 'border-red-600 text-red-600' : '' }}">
            Tous
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ request('status') === 'pending' ? 'border-red-600 text-red-600' : '' }}">
            En attente
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ request('status') === 'approved' ? 'border-red-600 text-red-600' : '' }}">
            Approuvés
        </a>
        <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 {{ request('status') === 'rejected' ? 'border-red-600 text-red-600' : '' }}">
            Rejetés
        </a>
    </div>

    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4 flex-1">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <p class="text-sm font-semibold text-gray-900">{{ $comment->user?->name ?? 'Utilisateur anonyme' }}</p>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                @if($comment->approved === null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En attente</span>
                                @elseif($comment->approved === true)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approuvé</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejeté</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Sur le signalement: <a href="{{ route('admin.reports.show', $comment->report) }}" class="text-blue-600 hover:text-blue-700">{{ $comment->report?->title ?? 'Signalement supprimé' }}</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-gray-50 p-4 mb-4 border border-gray-200">
                    <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
                </div>

                <div class="flex items-center space-x-3">
                    @if($comment->approved !== true)
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline-flex items-center">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approuver
                            </button>
                        </form>
                    @endif

                    @if($comment->approved !== false)
                        <form action="{{ route('admin.comments.reject', $comment) }}" method="POST" class="inline-flex items-center">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rejeter
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline-flex items-center ml-auto" onsubmit="return confirm('Êtes-vous sûr?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2l-4 4z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Aucun commentaire trouvé pour ce filtre.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($comments->hasPages())
        <div class="mt-6">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection
