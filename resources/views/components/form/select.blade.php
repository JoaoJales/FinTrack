@props([
    'required' => '',
    'name' => '',
    'id' => '',
    'placeholder' => '',
    'label' => '',
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

<div class="">
    @if ($label != 'none')
        <label for='{{ $name }}'
               class='mb-1 block text-xs text-slate-600  @if ($required != '') font-medium @endif'>{{ $label }}
            @if ($required != '')
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    <select name='{{ $name }}' id='{{ $name }}' {{ $required }}
        {{ $attributes->merge(['class' => 'min-h-[32px] py-[3px] px-3 w-full bg-white border border-slate-300 rounded-lg text-sm text-slate-900 shadow-sm outline-none transition-all duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer']) }}>

        @if ($placeholder != '')
            <option value class="text-slate-100">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @error($name)
    <p class="error text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
