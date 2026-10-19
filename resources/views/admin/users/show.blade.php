@extends('admin.layouts.app')

@section('title', __('admin.users_details_title'))

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ __('admin.users_profile') }}</h2>
        <p class="text-slate-600">{{ __('admin.users_account_details') }}</p>
    </div>

    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
        <dl class="grid gap-3 md:grid-cols-2">
            <div>
                <dt class="text-xs uppercase text-slate-500">{{ __('admin.name') }}</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">{{ __('admin.email') }}</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">{{ __('admin.administrator') }}</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $user->is_admin ? __('admin.yes') : __('admin.no') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-500">{{ __('admin.active') }}</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $user->active ? __('admin.yes') : __('admin.no') }}</dd>
            </div>
        </dl>

        <div class="mt-6 space-x-2 rtl:space-x-reverse">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-slate-200 px-4 py-2 text-sm">{{ __('admin.back') }}</a>
        </div>
    </div>
</div>
@endsection
