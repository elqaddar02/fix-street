@extends('admin.layouts.app')

@section('title', 'Signalement #' . $report->id)

@php
    $statusOptions = ['OPEN' => 'Ouvert', 'IN_PROGRESS' => 'En cours', 'RESOLVED' => 'Résolu', 'REJECTED' => 'Rejeté'];
    $hasImage = $report->image && file_exists(storage_path('app/public/' . $report->image));
    $hasMap = $report->latitude && $report->longitude;
    $card = 'rounded-xl border border-slate-200 bg-white';
    $cardTitle = 'text-sm font-semibold text-slate-900';
    $ref = '#' . str_pad((string) $report->id, 6, '0', STR_PAD_LEFT);
    $historyLabels = ['Status' => 'Statut modifié', 'Delete' => 'Supprimé', 'Update' => 'Modifié', 'Create' => 'Créé'];
@endphp

@section('content')
<div class="space-y-6">
    {{-- Header: what it is, where it stands, and the one decision to take. --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900">
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Signalements
            </a>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $report->title }}</h2>
            <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
                <span class="font-mono tabular-nums">{{ $ref }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ $report->category->display_name ?? '—' }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ $report->city->display_name ?? '—' }}</span>
                <span aria-hidden="true">·</span>
                <time datetime="{{ $report->created_at->toIso8601String() }}">{{ $report->created_at->format('d/m/Y H:i') }}</time>
            </p>
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2">
            <label for="report-status" class="text-sm font-medium text-slate-600">Statut</label>
            <select id="report-status" name="status" data-status-select
                    data-action="{{ route('admin.reports.updateStatus', $report) }}"
                    @class([
                        'rounded-lg border py-2 ps-3 pe-8 text-sm font-medium focus:ring-1 disabled:opacity-50',
                        'border-amber-200 bg-amber-50 text-amber-800 focus:border-amber-500 focus:ring-amber-500' => $report->status === 'OPEN',
                        'border-sky-200 bg-sky-50 text-sky-800 focus:border-sky-500 focus:ring-sky-500' => $report->status === 'IN_PROGRESS',
                        'border-emerald-200 bg-emerald-50 text-emerald-800 focus:border-emerald-500 focus:ring-emerald-500' => $report->status === 'RESOLVED',
                        'border-rose-200 bg-rose-50 text-rose-800 focus:border-rose-500 focus:ring-rose-500' => $report->status === 'REJECTED',
                    ])>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($report->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <a href="{{ route('reports.show', $report) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                Voir sur le site
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- MAIN: what the citizen saw --}}
        <div class="space-y-6 lg:col-span-2">
            <figure class="{{ $card }} overflow-hidden">
                @if($hasImage)
                    <a href="{{ asset('storage/' . $report->image) }}" target="_blank" rel="noopener" class="block bg-slate-100" title="Ouvrir la photo en taille réelle">
                        <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="max-h-[32rem] w-full object-contain">
                    </a>
                @else
                    <div class="flex aspect-[16/9] items-center justify-center bg-slate-50 text-sm text-slate-400">Aucune photo</div>
                @endif
            </figure>

            @if(filled($report->description) && $report->description !== $report->title)
                <section class="{{ $card }} p-5">
                    <h3 class="{{ $cardTitle }}">Description</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $report->description }}</p>
                </section>
            @endif

            @if($hasMap)
                <section class="{{ $card }} overflow-hidden">
                    <div class="flex items-baseline justify-between gap-3 px-5 pt-5">
                        <h3 class="{{ $cardTitle }}">Localisation</h3>
                        <a href="https://www.openstreetmap.org/?mlat={{ $report->latitude }}&mlon={{ $report->longitude }}#map=18/{{ $report->latitude }}/{{ $report->longitude }}"
                           target="_blank" rel="noopener" class="text-xs font-medium text-red-700 hover:underline" dir="ltr">
                            {{ number_format($report->latitude, 5) }}, {{ number_format($report->longitude, 5) }}
                        </a>
                    </div>
                    <div id="admin-report-map" class="mt-4 h-72 border-t border-slate-200" data-lat="{{ $report->latitude }}" data-lng="{{ $report->longitude }}"></div>
                </section>
            @endif

            @if(config('features.comments') && $report->comments->isNotEmpty())
                <section class="{{ $card }} p-5">
                    <h3 class="{{ $cardTitle }}">Commentaires ({{ $report->comments->count() }})</h3>
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach($report->comments as $comment)
                            <li class="py-3 text-sm">
                                <p class="font-medium text-slate-900">{{ $comment->user->name ?? 'Anonyme' }} <span class="font-normal text-slate-400">· {{ $comment->created_at->diffForHumans() }}</span></p>
                                <p class="mt-1 text-slate-700">{{ $comment->comment }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        {{-- SIDE: context for the decision --}}
        <aside class="space-y-6">
            <section class="{{ $card }} p-5">
                <h3 class="{{ $cardTitle }}">Détails</h3>
                <dl class="mt-3 divide-y divide-slate-100 text-sm">
                    @foreach([
                        'Catégorie' => $report->category->display_name ?? '—',
                        'Ville' => $report->city->display_name ?? '—',
                        'Arrondissement' => $report->district?->name_fr ?? '—',
                        'Quartier' => $report->quartier?->name_fr ?? '—',
                        'Créé le' => $report->created_at->format('d/m/Y H:i'),
                        'Mis à jour' => $report->updated_at->diffForHumans(),
                    ] as $label => $value)
                        <div class="flex justify-between gap-4 py-2">
                            <dt class="text-slate-500">{{ $label }}</dt>
                            <dd class="min-w-0 truncate text-end font-medium text-slate-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                    <div class="flex justify-between gap-4 py-2">
                        <dt class="text-slate-500">Signalé par</dt>
                        <dd class="min-w-0 truncate text-end font-medium">
                            @if($report->user)
                                <a href="{{ route('admin.users.show', $report->user) }}" class="text-red-700 hover:underline">{{ $report->user->name }}</a>
                            @else
                                <span class="text-slate-400">Compte supprimé</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="{{ $card }} p-5">
                <div class="flex items-baseline justify-between">
                    <h3 class="{{ $cardTitle }}">Confirmations</h3>
                    <span class="text-2xl font-semibold tabular-nums text-slate-900">{{ $report->likes_count }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Voisins qui confirment que le problème est réel et toujours présent.</p>
                @if($recentConfirmations->isNotEmpty())
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach($recentConfirmations as $like)
                            <li class="flex items-center justify-between gap-3">
                                <span class="truncate text-slate-700">{{ $like->user->name ?? 'Compte supprimé' }}</span>
                                <span class="shrink-0 text-xs text-slate-400">{{ $like->created_at?->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @if($report->dossier)
                <section class="{{ $card }} border-sky-200 bg-sky-50/60 p-5">
                    <h3 class="{{ $cardTitle }}">Dossier</h3>
                    <p class="mt-1 text-sm text-slate-700">{{ $report->dossier->title }}</p>
                    <p class="mt-1 text-xs text-slate-500">Rattaché à un dossier : la photo et la localisation sont figées, et le signalement ne peut pas être supprimé.</p>
                </section>
            @endif

            <section class="{{ $card }} p-5">
                <h3 class="{{ $cardTitle }}">Historique</h3>
                @if($history->isEmpty())
                    <p class="mt-2 text-sm text-slate-500">Aucune action d’administration pour l’instant.</p>
                @else
                    <ol class="mt-3 space-y-3">
                        @foreach($history as $entry)
                            <li class="relative ps-4 text-sm before:absolute before:start-0 before:top-1.5 before:h-2 before:w-2 before:rounded-full before:bg-slate-300">
                                <p class="text-slate-900">
                                    <span class="font-medium">{{ $historyLabels[$entry->action] ?? $entry->action }}</span>
                                    @if($entry->description)<span class="text-slate-600"> : {{ $entry->description }}</span>@endif
                                </p>
                                <p class="text-xs text-slate-400">{{ $entry->admin->name ?? 'Admin' }} · {{ $entry->created_at->format('d/m/Y H:i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            @unless($report->dossier_id)
                <form method="POST" action="{{ route('admin.reports.destroy', $report) }}"
                      onsubmit="return confirm(@js('Supprimer définitivement « ' . $report->title . ' » et sa photo ?'))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-rose-200 bg-white px-4 py-2.5 text-sm font-medium text-rose-700 hover:bg-rose-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        Supprimer le signalement
                    </button>
                </form>
            @endunless
        </aside>
    </div>
</div>
@endsection

@if($hasMap)
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            (function () {
                const el = document.getElementById('admin-report-map');
                if (!el || typeof L === 'undefined') return;
                const point = [parseFloat(el.dataset.lat), parseFloat(el.dataset.lng)];
                const map = L.map(el, { scrollWheelZoom: false }).setView(point, 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
                L.marker(point, {
                    icon: L.icon({
                        iconUrl: @js(asset('images/marker-icon.png')),
                        iconRetinaUrl: @js(asset('images/marker-icon-2x.png')),
                        shadowUrl: @js(asset('images/marker-shadow.png')),
                        iconSize: [25, 41], iconAnchor: [12, 41], shadowSize: [41, 41],
                    }),
                }).addTo(map);
            })();
        </script>
    @endpush
@endif
