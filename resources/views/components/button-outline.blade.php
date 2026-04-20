@props(['type' => 'submit'])

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'py-1 px-2 rounded-md text-sm inline-flex items-center border-2 border-blue-500 text-blue-900 hover:bg-blue-100']) }}>
    {{ $slot }}
</button>
