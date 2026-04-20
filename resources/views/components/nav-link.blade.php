@props(['active' => false])

@php
    $classes = $active
        ? 'text-sm font-semibold text-primary bg-blue-100 border-b-2 border-blue-400 px-4 py-2 rounded-lg transition'
        : 'text-sm font-medium text-gray-600 hover:border-b-2 hover:border-gray-400 hover:text-gray-900 hover:bg-gray-300 px-4 py-2 rounded-lg transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
