@props(['message' => null])

@php
    $message = $message ?? session('error');
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm font-medium text-red-700']) }}>
        {{ $message }}
    </div>
@endif
