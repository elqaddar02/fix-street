@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500']) }}>
