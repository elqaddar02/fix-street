@extends('admin.layouts.app')

@section('title', __('admin.categories_edit_title'))

@section('content')
<div class="space-y-8 max-w-2xl">
    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ __('admin.categories_edit_title') }}</h2>
        <p class="text-slate-600">{{ __('admin.categories_edit_intro') }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">{{ __('admin.name_fr') }}</label>
                <input type="text" id="name" name="name" required value="{{ old('name', $category->name) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name Arabic -->
            <div>
                <label for="name_ar" class="block text-sm font-medium text-slate-700 mb-2">{{ __('admin.name_ar') }}</label>
                <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name_ar') border-red-500 @enderror" dir="rtl">
                @error('name_ar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-6 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('admin.save_changes') }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    {{ __('admin.cancel') }}
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="ms-auto" onsubmit="return confirm(@js(__('admin.confirm_irreversible')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('admin.delete') }}
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
