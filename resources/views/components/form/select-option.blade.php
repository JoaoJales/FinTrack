@props([
    'value' => '',
    'select' => ''
])

<option value="{{ $value }}"
    @if($select == $value) selected="selected" @endif
    {{ $attributes->merge(['class' => 'text-slate-900 bg-white']) }}>
    {{ $slot }}
</option>
