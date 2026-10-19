@extends('admin.layouts.app')

@section('title', __('admin.ads'))

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ __('admin.ads') }}</h2>
            <p class="text-slate-600">{{ __('admin.ads_intro') }}</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('admin.ads_new') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.ad') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.ads_placement') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.status') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.ads_window') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.ads_impressions') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.ads_clicks') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-medium text-slate-700 uppercase tracking-wider">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($ads as $ad)
                        @php($badge = adStatusBadge($ad))
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($ad->image)
                                        <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="h-10 w-16 rounded border border-slate-200 object-cover">
                                    @else
                                        <div class="h-10 w-16 rounded border border-dashed border-slate-300 bg-slate-50"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $ad->title }}</p>
                                        <p class="text-xs text-slate-500">{{ ucfirst($ad->provider) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $ad->placement->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $ad->starts_at?->format('d/m/Y H:i') ?? '—' }}
                                →
                                {{ $ad->ends_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $ad->impressions_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $ad->clicks_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-end space-x-2 rtl:space-x-reverse">
                                <form action="{{ route('admin.ads.updateStatus', $ad) }}" method="POST" class="inline-flex items-center">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $ad->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-slate-600 hover:text-slate-900">
                                        {{ $ad->is_active ? __('admin.deactivate') : __('admin.activate') }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.ads.edit', $ad) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-slate-600 hover:text-slate-900">
                                    {{ __('admin.edit') }}
                                </a>
                                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm(@js(__('admin.are_you_sure')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 hover:text-red-700">
                                        {{ __('admin.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                <p>{{ __('admin.ads_empty') }}</p>
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
