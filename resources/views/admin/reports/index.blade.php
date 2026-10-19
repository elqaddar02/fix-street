@extends('admin.layouts.app')

@section('title', 'Signalements')

@section('content')
@php
    $statusOptions = [
        'OPEN' => 'Ouvert',
        'IN_PROGRESS' => 'En cours',
        'RESOLVED' => 'Résolu',
        'REJECTED' => 'Rejeté',
    ];
    $currentStatus = request('status');
@endphp

<div class="space-y-6">

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Signalements</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $reports->total() }} signalement(s)</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3">
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('admin.reports.index', array_filter(['sort' => $sortBy, 'q' => $search, 'category' => $categoryId, 'city' => $cityId])) }}"
               @class([
                   'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                   'bg-slate-900 text-white' => ! $currentStatus,
                   'text-slate-600 hover:bg-slate-100' => (bool) $currentStatus,
               ])>Tous</a>
            @foreach($statusOptions as $value => $label)
                <a href="{{ route('admin.reports.index', array_filter(['status' => $value, 'sort' => $sortBy, 'q' => $search, 'category' => $categoryId, 'city' => $cityId])) }}"
                   @class([
                       'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                       'bg-slate-900 text-white' => $currentStatus === $value,
                       'text-slate-600 hover:bg-slate-100' => $currentStatus !== $value,
                   ])>{{ $label }}</a>
            @endforeach
        </div>

        <div class="flex w-full flex-wrap items-center gap-2 lg:ms-auto lg:w-auto">
            @if($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            <label for="q" class="sr-only">Rechercher</label>
            <div class="relative min-w-0 basis-full lg:w-56 lg:basis-auto">
                <svg class="pointer-events-none absolute start-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Titre, #réf., utilisateur…"
                       class="w-full rounded-lg border border-slate-300 py-1.5 ps-8 pe-3 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <label for="category" class="sr-only">Catégorie</label>
            <select id="category" name="category" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 py-1.5 ps-3 pe-8 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Toutes catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->display_name }}</option>
                @endforeach
            </select>
            <label for="city" class="sr-only">Ville</label>
            <select id="city" name="city" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 py-1.5 ps-3 pe-8 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Toutes villes</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" @selected((string) $cityId === (string) $city->id)>{{ $city->display_name }}</option>
                @endforeach
            </select>
            <label for="sort" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Trier</label>
            <select id="sort" name="sort" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="latest" @selected($sortBy === 'latest')>Plus récents</option>
                <option value="oldest" @selected($sortBy === 'oldest')>Plus anciens</option>
                <option value="mostLiked" @selected($sortBy === 'mostLiked')>Plus soutenus</option>
            </select>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.reports.bulkUpdateStatus') }}" id="bulk-action-form"
          class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        @csrf

        {{-- Bulk bar: only meaningful once rows are selected, so it stays out of the way until then. --}}
        <div id="bulk-bar"
             class="hidden flex-wrap items-center gap-3 border-b border-slate-200 bg-slate-50 px-6 py-3">
            <span class="text-sm font-medium text-slate-700">
                <span id="bulk-count">0</span> sélectionné(s)
            </span>
            <label for="bulk-status" class="sr-only">Statut à appliquer</label>
            <select id="bulk-status" name="status"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Choisir un statut…</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-red-700">
                Appliquer
            </button>
        </div>

        <div class="overflow-x-auto">
            {{-- Below md the rows stack into cards; same cells, so no duplicate inputs inside the bulk form. --}}
            <table class="block min-w-full text-left md:table">
                <thead class="hidden md:table-header-group">
                    <tr class="border-b border-slate-200 bg-slate-50/80">
                        <th scope="col" class="w-10 px-6 py-3">
                            <input id="select-all" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <label for="select-all" class="sr-only">Tout sélectionner</label>
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Signalement</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Ville</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Confirmations</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="block divide-y divide-slate-100 md:table-row-group">
                    @forelse($reports as $report)
                        <tr class="grid grid-cols-[1rem_1fr_auto_auto] items-center gap-x-3 gap-y-2 px-4 py-3 transition-colors hover:bg-slate-50/60 md:table-row md:p-0">
                            <td class="self-start pt-3.5 md:px-6 md:py-3.5">
                                <input type="checkbox" name="report_ids[]" value="{{ $report->id }}"
                                       class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="sr-only">Sélectionner {{ $report->title }}</span>
                            </td>
                            <td class="col-span-3 min-w-0 md:px-6 md:py-3">
                                @php($hasImage = $report->image && file_exists(storage_path('app/public/' . $report->image)))
                                <a href="{{ route('admin.reports.show', $report) }}" class="group flex min-w-0 items-center gap-3">
                                    <span class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-slate-100 ring-1 ring-inset ring-slate-200">
                                        @if($hasImage)
                                            <img src="{{ asset('storage/' . $report->image) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                        @else
                                            <span class="flex h-full w-full items-center justify-center text-slate-300">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                                            </span>
                                        @endif
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block max-w-xs truncate text-sm font-medium text-slate-900 group-hover:text-red-700">{{ $report->title }}</span>
                                        <span class="block truncate text-xs text-slate-500"><span class="font-mono">#{{ str_pad((string) $report->id, 6, '0', STR_PAD_LEFT) }}</span> · {{ $report->category->display_name ?? '—' }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="col-start-2 md:px-6 md:py-3.5">
                                <span class="text-xs text-slate-500 md:text-sm md:text-slate-600">{{ $report->city->display_name ?? '—' }}</span>
                            </td>
                            <td class="col-span-2 whitespace-nowrap text-right md:px-6 md:py-3.5 md:text-left">
                                <time class="text-xs text-slate-500 md:text-sm md:text-slate-600" datetime="{{ $report->created_at->toIso8601String() }}" title="{{ $report->created_at->format('d/m/Y H:i') }}">{{ $report->created_at->format('d/m/Y') }}</time>
                            </td>
                            <td class="col-start-2 md:px-6 md:py-3.5">
                                {{--
                                    No nested <form> here: this row sits inside the bulk form and
                                    nested forms are invalid HTML. The select posts on its own.
                                --}}
                                <label class="sr-only" for="status-{{ $report->id }}">Statut de {{ $report->title }}</label>
                                <select id="status-{{ $report->id }}"
                                        name="status"
                                        data-status-select
                                        data-action="{{ route('admin.reports.updateStatus', $report) }}"
                                        @class([
                                            'rounded-lg border py-1.5 ps-2.5 pe-8 text-xs font-medium transition-colors focus:ring-1 disabled:opacity-50',
                                            'border-amber-200 bg-amber-50 text-amber-800 focus:border-amber-500 focus:ring-amber-500' => $report->status === 'OPEN',
                                            'border-sky-200 bg-sky-50 text-sky-800 focus:border-sky-500 focus:ring-sky-500' => $report->status === 'IN_PROGRESS',
                                            'border-emerald-200 bg-emerald-50 text-emerald-800 focus:border-emerald-500 focus:ring-emerald-500' => $report->status === 'RESOLVED',
                                            'border-rose-200 bg-rose-50 text-rose-800 focus:border-rose-500 focus:ring-rose-500' => $report->status === 'REJECTED',
                                        ])>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($report->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="md:px-6 md:py-3.5">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold',
                                    'bg-amber-50 text-amber-700' => $report->likes_count > 0,
                                    'bg-slate-50 text-slate-400' => ! $report->likes_count,
                                ])>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $report->likes_count ?? 0 }}
                                </span>
                            </td>
                            <td class="md:px-6 md:py-3.5">
                                <div class="-me-2 flex items-center justify-end gap-1 md:me-0">
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                       class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                                       title="Détails">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span class="sr-only">Détails de {{ $report->title }}</span>
                                    </a>
                                    <button type="button"
                                            data-delete-report="{{ route('admin.reports.destroy', $report) }}"
                                            data-delete-label="{{ $report->title }}"
                                            class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                            title="Supprimer">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span class="sr-only">Supprimer {{ $report->title }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="7" class="block px-6 py-16 text-center md:table-cell">
                                <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-slate-600">Aucun signalement trouvé</p>
                                @if($currentStatus || $search || $categoryId || $cityId)
                                    <a href="{{ route('admin.reports.index') }}" class="mt-1 inline-block text-sm text-red-600 hover:text-red-700">Retirer les filtres</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="border-t border-slate-200 px-6 py-3">
                {{ $reports->links() }}
            </div>
        @endif
    </form>
</div>

{{-- Delete posts through a form kept outside the bulk form, again to avoid nesting. --}}
<form id="delete-report-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    (function () {
        const bulkForm = document.getElementById('bulk-action-form');
        const bar = document.getElementById('bulk-bar');
        const counter = document.getElementById('bulk-count');
        const selectAll = document.getElementById('select-all');
        const boxes = () => bulkForm.querySelectorAll('input[name="report_ids[]"]');

        function syncCount() {
            const all = boxes();
            const count = bulkForm.querySelectorAll('input[name="report_ids[]"]:checked').length;
            counter.textContent = count;
            // Toggle classes, not the hidden attribute: the bar's display:flex overrode it.
            bar.classList.toggle('hidden', count === 0);
            bar.classList.toggle('flex', count > 0);
            selectAll.checked = count > 0 && count === all.length;
            selectAll.indeterminate = count > 0 && count < all.length;
        }

        selectAll?.addEventListener('change', (event) => {
            boxes().forEach((box) => { box.checked = event.target.checked; });
            syncCount();
        });

        bulkForm.addEventListener('change', (event) => {
            if (event.target.name === 'report_ids[]') syncCount();
        });

        bulkForm.addEventListener('submit', (event) => {
            const count = bulkForm.querySelectorAll('input[name="report_ids[]"]:checked').length;
            if (!count || !document.getElementById('bulk-status').value) {
                event.preventDefault();
                return;
            }
            if (!confirm(`Appliquer ce statut à ${count} signalement(s) ?`)) {
                event.preventDefault();
            }
        });

        // Delete buttons submit the standalone form so no <form> is nested.
        const deleteForm = document.getElementById('delete-report-form');
        document.querySelectorAll('[data-delete-report]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!confirm(`Supprimer définitivement « ${button.dataset.deleteLabel} » ?`)) return;
                deleteForm.action = button.dataset.deleteReport;
                deleteForm.submit();
            });
        });
    })();
</script>
@endpush
@endsection
