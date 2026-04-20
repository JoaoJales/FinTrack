@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => '',
    'rows' => 3,
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
        <label for="{{ $name }}" class="block text-xs font-medium leading-5 text-slate-700">{{ $label }}
            @if ($required != '')
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    <div class="mt-1 rounded-md shadow-sm">
        <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}"
            {{ $attributes->merge(['class' => 'block w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 shadow-sm outline-none transition-all duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20 resize-y min-h-[80px]']) }}>
            {{ $slot }}
        </textarea>

        @error($name)
            <p class="error text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
