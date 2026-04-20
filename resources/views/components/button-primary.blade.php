@props(['type' => 'submit'])

<button {{ $attributes->merge([
    'type' => $type,
    'class' => 'bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/30 transition duration-200 flex items-center'
]) }}>
    {{ $slot }}
</button>
