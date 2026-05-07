<div
    {{ $attributes->merge(['class' => 'relative overflow-x-auto overflow-y-auto -mx-1 px-1 [-webkit-overflow-scrolling:touch]']) }}>
    <p class="sm:hidden text-xs text-gray-400 mb-2 text-center">Deslize horizontalmente para ver todas as colunas</p>
    {{ $slot }}
</div>
