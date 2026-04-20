{{--@props([--}}
{{--    'placeholder' => 'Select an option',--}}
{{--    'label' => '',--}}
{{--    'required' => '',--}}
{{--    'name' => '',--}}
{{--    'id' => '',--}}
{{--    'url' => '',--}}
{{--    'options' => '',--}}
{{--])--}}

{{--<div x-data="{--}}
{{--    multiple: true,--}}
{{--    value: @entangle($attributes->wire('model')).live,--}}
{{--    @if($options)--}}
{{--    options: [--}}
{{--        @foreach($options as $option) { label: '{{ $option['label'] }}', value: {{ $option['value'] }} },--}}
{{--        @endforeach--}}
{{--    ],--}}
{{--    @else--}}
{{--    options: [],--}}
{{--    @endif--}}
{{--    init() {--}}
{{--        let bootSelect2 = () => {--}}
{{--            let selections = this.multiple ? this.value : [this.value];--}}
{{--            if (!this.value) {--}}
{{--                this.$refs.{{ $id }}.innerHTML = '';--}}
{{--            }--}}

{{--            jQuery(this.$refs.{{ $id }}).select2({--}}
{{--                multiple: this.multiple,--}}
{{--                placeholder: '{{ $placeholder }}',--}}
{{--                language: 'pt-BR',--}}
{{--                allowClear: false,--}}
{{--                data: this.options.map(i => ({--}}
{{--                    id: i.value,--}}
{{--                    text: i.label,--}}
{{--                    selected: selections ? selections.map(i => String(i)).includes(String(i.value)) : false,--}}
{{--                }))--}}
{{--            });--}}
{{--            jQuery(this.$refs.{{ $id }}).val(this.value);--}}
{{--            jQuery(this.$refs.{{ $id }}).trigger('change');--}}
{{--        }--}}
{{--        let refreshSelect2 = () => {--}}
{{--            if (!this.value || this.options.length === 0) {--}}
{{--                this.$refs.{{ $id }}.innerHTML = '';--}}
{{--            }--}}
{{--            jQuery(this.$refs.{{ $id }}).trigger('change');--}}
{{--            bootSelect2();--}}
{{--        }--}}

{{--        bootSelect2();--}}

{{--        jQuery(this.$refs.{{ $id }}).on('select2:select', () => {--}}
{{--            let currentSelection = jQuery(this.$refs.{{ $id }}).select2('data');--}}

{{--            this.value = this.multiple ?--}}
{{--                currentSelection.map(i => i.id) :--}}
{{--                currentSelection[0].id;--}}
{{--        });--}}
{{--        jQuery(this.$refs.{{ $id }}).on('select2:unselect', (e) => {--}}
{{--            this.value = this.value.filter(i => i != parseInt(e.params.data.id));--}}
{{--        });--}}

{{--        this.$watch('value', () => refreshSelect2());--}}
{{--        this.$watch('options', () => refreshSelect2());--}}
{{--        window.addEventListener('update-{{ $name }}-values', refreshSelect2());--}}
{{--    },--}}
{{--}" class="flex flex-col items-center relative w-full">--}}
{{--    <div class="w-full" wire:ignore>--}}
{{--        @if ($label != 'none')--}}
{{--            <label for="{{ $name }}"--}}
{{--                   class="mb-1 block text-xs text-slate-700 dark:text-slate-200 @if ($required != '') font-medium @endif">--}}
{{--                {{ $label }}--}}
{{--                @if ($required != '')--}}
{{--                    <span class="text-red-600">*</span>--}}
{{--                @endif--}}
{{--            </label>--}}
{{--        @endif--}}
{{--        <select x-ref="{{ $id }}" id="{{ $id }}" name="{{ $id }}" style="width: 100%"--}}
{{--            {{ $required != '' ? 'required=""' : '' }}>--}}
{{--            {{ $slot }}--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    @error($attributes->wire('model')->value)--}}
{{--    <p class="error text-xs text-red-600">{{ $message }}</p>--}}
{{--    @enderror--}}
{{--</div>--}}
