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
    <form method="GET" class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-3">
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('admin.reports.index', array_filter(['sort' => $sortBy])) }}"
               @class([
                   'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                   'bg-slate-900 text-white' => ! $currentStatus,
                   'text-slate-600 hover:bg-slate-100' => (bool) $currentStatus,
               ])>Tous</a>
            @foreach($statusOptions as $value => $label)
                <a href="{{ route('admin.reports.index', array_filter(['status' => $value, 'sort' => $sortBy])) }}"
                   @class([
                       'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                       'bg-slate-900 text-white' => $currentStatus === $value,
                       'text-slate-600 hover:bg-slate-100' => $currentStatus !== $value,
                   ])>{{ $label }}</a>
            @endforeach
        </div>

        <div class="ml-auto flex items-center gap-2">
            @if($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            <label for="sort" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Trier</label>
            <select id="sort" name="sort" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 px-3 py-1.5 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="latest" @selected($sortBy === 'latest')>Plus récents</option>
                <option value="oldest" @selected($sortBy === 'oldest')>Plus anciens</option>
                <option value="mostLiked" @selected($sortBy === 'mostLiked')>Plus soutenus</option>
            </select>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.reports.bulkUpdateStatus') }}" id="bulk-action-form"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @csrf

        {{-- Bulk bar: only meaningful once rows are selected, so it stays out of the way until then. --}}
        <div id="bulk-bar" hidden
             class="flex flex-wrap items-center gap-3 border-b border-slate-200 bg-slate-50 px-6 py-3">
            <span class="text-sm font-medium text-slate-700">
                <span id="bulk-count">0</span> sélectionné(s)
            </span>
            <label for="bulk-status" class="sr-only">Statut à appliquer</label>
            <select id="bulk-status" name="status"
                    class="rounded-xl border border-slate-300 px-3 py-1.5 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="">Choisir un statut…</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-red-600 px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-red-700">
                Appliquer
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80">
                        <th scope="col" class="w-10 px-6 py-3">
                            <input id="select-all" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <label for="select-all" class="sr-only">Tout sélectionner</label>
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Signalement</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Ville</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Soutiens</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-6 py-3.5">
                                <input type="checkbox" name="report_ids[]" value="{{ $report->id }}"
                                       class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="sr-only">Sélectionner {{ $report->title }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-medium">{{ $report->title }}</p>
                                <p class="text-xs text-slate-500">{{ Str::limit($report->description, 60) }}</p>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="text-sm text-slate-600">{{ $report->city->display_name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3.5">
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
                                            'rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors focus:ring-1 disabled:opacity-50',
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
                            <td class="px-6 py-3.5">
                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold',
                                    'bg-red-50 text-red-600' => $report->likes_count > 0,
                                    'bg-slate-50 text-slate-400' => ! $report->likes_count,
                                ])>
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    {{ $report->likes_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
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
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-slate-600">Aucun signalement trouvé</p>
                                @if($currentStatus)
                                    <a href="{{ route('admin.reports.index') }}" class="mt-1 inline-block text-sm text-red-600 hover:text-red-700">Retirer le filtre</a>
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
            bar.hidden = count === 0;
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
