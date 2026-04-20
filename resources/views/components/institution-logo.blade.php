@props([
    'image' => '',
    'name' => 'Logo',
    'white' => false, // Controla se deve ficar branco ou não
    'size' => 'w-8 h-8' // Tamanho padrão que pode ser sobrescrito
])

<img
    {{-- O asset() gera a URL completa para a pasta public--}}
    src="{{ asset($image) }}"
    alt="{{ $name }}"
    {{ $attributes->merge(['class' => $size . ' object-contain ' . ($white ? 'brightness-0 invert' : '')]) }}
>
