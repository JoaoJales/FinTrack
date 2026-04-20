@props(['type' => 'submit'])

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'py-1 px-2 rounded-lg text-base inline-flex items-center bg-primary hover:bg-primary-dark text-white']) }}>
    {{ $slot }}
</button>

