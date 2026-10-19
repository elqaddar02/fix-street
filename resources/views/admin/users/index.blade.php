@extends('admin.layouts.app')

@section('title', __('admin.users'))

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">{{ __('admin.users') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('admin.accounts_count', ['count' => $users->total()]) }}</p>
        </div>

        <form method="GET" class="flex w-full max-w-sm gap-2">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                </span>
                <input name="search" value="{{ $search ?? '' }}"
                       placeholder="{{ __('admin.users_search_placeholder') }}"
                       class="w-full rounded-lg border border-slate-300 py-2 ps-9 pe-3 text-sm placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-slate-800">
                {{ __('admin.search_button') }}
            </button>
            @if($search ?? false)
                <a href="{{ route('admin.users.index') }}" class="flex items-center rounded-lg px-3 text-sm font-medium text-slate-500 hover:text-slate-800">
                    {{ __('admin.clear') }}
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            {{-- Below md the rows stack into cards. --}}
            <table class="block min-w-full text-start md:table">
                <thead class="hidden md:table-header-group">
                    <tr class="border-b border-slate-200 bg-slate-50/80">
                        <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('admin.user') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('admin.joined') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('admin.reports') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('admin.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-end text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="block divide-y divide-slate-100 md:table-row-group">
                    @forelse($users as $user)
                        <tr class="grid grid-cols-[auto_auto_1fr] items-center gap-x-4 gap-y-2 px-4 py-3 transition-colors hover:bg-slate-50/60 md:table-row md:p-0">
                            <td class="order-1 col-span-2 min-w-0 md:order-none md:px-6 md:py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium">
                                            {{ $user->name }}
                                            @if($user->is_admin)
                                                <span class="ms-1 rounded bg-red-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-red-700">{{ __('admin.admin') }}</span>
                                            @endif
                                        </p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="order-3 md:order-none md:px-6 md:py-3.5">
                                <span class="whitespace-nowrap text-xs text-slate-500 md:text-sm"><span class="md:hidden">{{ __('admin.joined_on') }} </span>{{ $user->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="order-4 text-xs tabular-nums text-slate-500 md:order-none md:px-6 md:py-3.5 md:text-sm md:text-slate-600"><span class="md:hidden">{{ __('admin.reports_count', ['count' => $user->reports_count]) }}</span><span class="hidden md:inline">{{ $user->reports_count }}</span></td>
                            <td class="order-5 justify-self-end md:order-none md:px-6 md:py-3.5">
                                @php($protected = $user->is_admin || $user->is(auth()->user()))
                                @if($protected)
                                    {{-- Admins (and you) can't be deactivated from here, so nobody locks the panel. --}}
                                    <span class="inline-flex items-center gap-1.5 text-sm text-slate-500" title="{{ __('admin.users_protected_hint') }}">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        {{ __('admin.protected') }}
                                    </span>
                                @else
                                    <x-admin.status-toggle
                                        :action="route('admin.users.updateStatus', $user)"
                                        :active="(bool) $user->active"
                                        :label="$user->name" />
                                @endif
                            </td>
                            <td class="order-2 md:order-none md:px-6 md:py-3.5">
                                <div class="-me-2 flex items-center justify-end gap-1 md:me-0">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                                       title="{{ __('admin.view') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span class="sr-only">{{ __('admin.view_name', ['name' => $user->name]) }}</span>
                                    </a>
                                    @unless($protected)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm(@js(__('admin.confirm_delete_user', ['name' => $user->name]) . ($user->reports_count > 0 ? ' ' . __('admin.confirm_delete_user_reports', ['count' => $user->reports_count]) : '')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                                    title="{{ __('admin.delete') }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span class="sr-only">{{ __('admin.delete_name', ['name' => $user->name]) }}</span>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block px-6 py-16 text-center md:table-cell">
                                <p class="text-sm font-medium text-slate-600">{{ __('admin.users_empty') }}</p>
                                @if($search ?? false)
                                    <p class="mt-1 text-sm text-slate-400">{{ __('admin.no_results_for', ['search' => $search]) }}</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-200 px-6 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
