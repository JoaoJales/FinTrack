@props(['value', 'color' => 'blue'])

@php
    // Mapeamento de cores para manter a compatibilidade com o JIT do Tailwind
    $activeStyles = match($color) {
        'emerald', 'green' => 'border-emerald-600 text-emerald-700 bg-emerald-50',
        'red', 'rose'     => 'border-rose-600 text-rose-700 bg-rose-50',
        'amber', 'orange' => 'border-amber-600 text-amber-700 bg-amber-50',
        'indigo'          => 'border-indigo-600 text-indigo-700 bg-indigo-50',
        default           => 'border-blue-600 text-blue-700 bg-blue-50',
    };
@endphp

<button type="button"
        @click="active = '{{ $value }}'"
        :class="active === '{{ $value }}'
    ? '{{ $activeStyles }} font-bold'
    : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
    {{ $attributes->merge(['class' => 'whitespace-nowrap py-3 px-8 border-b-2 text-sm transition-all -mb-px rounded-t-lg']) }}>
    {{ $slot }}
</button>
