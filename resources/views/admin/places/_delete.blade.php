{{-- A place still in use can't be deleted: its reports would silently lose their area. --}}
@php($trash = 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16')
@if($blocked)
    <span class="cursor-not-allowed rounded-lg p-2 text-slate-200" title="{{ $reason }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $trash }}"/></svg>
        <span class="sr-only">{{ $reason }}</span>
    </span>
@else
    <form action="{{ $action }}" method="POST" onsubmit="return confirm(@js(__('admin.confirm_delete_named', ['name' => $name])))">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600" title="{{ __('admin.delete') }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $trash }}"/></svg>
            <span class="sr-only">{{ __('admin.delete_name', ['name' => $name]) }}</span>
        </button>
    </form>
@endif
