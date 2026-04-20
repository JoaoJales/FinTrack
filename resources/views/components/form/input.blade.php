@props([
    'required' => '',
    'type' => 'text',
    'name' => '',
    'label' => '',
    'value' => '',
    'id' => '',
])

@if ($label === 'none')
@elseif ($label === '')
    @php
        //remove underscores from name
        $label = str_replace('_', ' ', $name);
        //detect subsequent letters starting with a capital
        $label = preg_split('/(?=[A-Z])/', $label);
        //display capital words with a space
        $label = implode(' ', $label);
        //uppercase first letter and lower the rest of a word
        $label = ucwords(strtolower($label));
    @endphp
@endif

<div>
    @if ($label != 'none')
        <label for="{{ $id }}"
               class="mb-1 block text-xs text-slate-700  @if ($required != '') font-medium @endif">{{ $label }}
            @if ($required != '')
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    <div>
        <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $slot }}"
            {{ $required }}
        {{ $attributes->merge(['class' => 'min-h-[32px] py-[3px] px-3 block w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 shadow-sm outline-none transition-all duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20']) }}>
        @error($name)
        <p class="error text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
