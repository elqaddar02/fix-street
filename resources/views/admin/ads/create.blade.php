@extends('admin.layouts.app')

@section('title', __('admin.ads_create_title'))

@section('content')
<div class="space-y-8 max-w-2xl">
    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ __('admin.ads_create_title') }}</h2>
        <p class="text-slate-600">{{ __('admin.ads_create_intro') }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @include('admin.ads._form', ['placements' => $placements])

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-6 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('admin.ads_create_button') }}
                </button>
                <a href="{{ route('admin.ads.index') }}" class="inline-flex items-center px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
