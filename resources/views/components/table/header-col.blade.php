@props(['centered' => false])

<th {{ $attributes->merge(['class' => ($centered ? 'text-center ' : 'text-left ') . 'py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider']) }}>
    <div @class(['flex justify-center items-center' => $centered])>
        {{ $slot }}
    </div>
</th>
