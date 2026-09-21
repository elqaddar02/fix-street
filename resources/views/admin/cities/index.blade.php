@extends('admin.layouts.app')

@section('title', 'Villes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Villes</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $cities->total() }} ville(s) · activez ou désactivez directement dans la liste.</p>
        </div>
    </div>

    {{-- Create --}}
    <form method="POST" action="{{ route('admin.cities.store') }}"
          class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4">
        @csrf
        <div class="min-w-[220px] flex-1">
            <label for="city-name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nouvelle ville</label>
            <input id="city-name" name="name" required
                   placeholder="Nom de la ville"
                   class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500">
        </div>
        <div>
            <label for="city-active" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</label>
            <select id="city-active" name="active"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700">
            Ajouter
        </button>
        @error('name')
            <p class="w-full text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </form>

    {{-- List --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80">
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Nom</th>
                        <th scope="col" class="px-6 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cities as $city)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-6 py-3.5">
                                <span class="text-sm font-medium">{{ $city->name }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <x-admin.status-toggle
                                    :action="route('admin.cities.updateStatus', $city)"
                                    :active="(bool) $city->active"
                                    :label="$city->name"
                                    feminine />
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.cities.edit', $city) }}"
                                       class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                                       title="Modifier">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span class="sr-only">Modifier {{ $city->name }}</span>
                                    </a>
                                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST"
                                          onsubmit="return confirm('Supprimer définitivement « {{ $city->name }} » ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                                title="Supprimer">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span class="sr-only">Supprimer {{ $city->name }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <p class="text-sm font-medium text-slate-600">Aucune ville</p>
                                <p class="mt-1 text-sm text-slate-400">Ajoutez-en une avec le formulaire ci-dessus.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cities->hasPages())
            <div class="border-t border-slate-200 px-6 py-3">
                {{ $cities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
