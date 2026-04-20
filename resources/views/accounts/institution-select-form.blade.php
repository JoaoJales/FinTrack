<x-modal name="institution-select" title="Selecione o Banco" width="md:w-1/4">
    <div class="grid grid-cols-1 gap-3">
        @foreach($institutions as $institution)
            <button
                type="button"
                class="w-full p-3 bg-gray-50 hover:bg-blue-50 border-2 border-gray-100 hover:border-blue-400 flex items-center gap-3 rounded-xl transition cursor-pointer"
                x-on:click="
                    selectedInstitution = {
                        id: {{ $institution->id }},
                        name: '{{ addslashes($institution->name) }}',
                        image: '{{ $institution->image }}'
                    };
                    $dispatch('close-modal', 'institution-select');
                    $dispatch('institution-selected', selectedInstitution);
                "
            >
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                    <x-institution-logo :image="$institution->image" :alt="$institution->name"/>
                </div>
                <div class="flex items-center justify-between w-full">
                    <div><p class="font-semibold text-gray-800">{{ $institution->name }}</p></div>
                </div>
            </button>
        @endforeach
    </div>
</x-modal>

{{--<x-modal name="institution-select" width="md:w-1/2">--}}
{{--    <x-slot name="headerTitle">--}}
{{--        <span>Selecione o Banco</span>--}}
{{--    </x-slot>--}}

{{--    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">--}}
{{--        @foreach($institutions as $institution)--}}
{{--            <button--}}
{{--                type="button"--}}
{{--                class="flex flex-col items-center gap-2 p-3 rounded-xl border border-gray-100 hover:border-blue-400 hover:bg-blue-50 transition"--}}
{{--                x-on:click="--}}
{{--                    selectedInstitution = {--}}
{{--                        id: {{ $institution->id }},--}}
{{--                        name: '{{ addslashes($institution->name) }}',--}}
{{--                        image: '{{ $institution->image }}'--}}
{{--                    };--}}
{{--                    $dispatch('close-modal', 'institution-select');--}}
{{--                    $dispatch('institution-selected', selectedInstitution);--}}
{{--                "--}}
{{--            >--}}
{{--                <img src="{{ $institution->image }}" alt="{{ $institution->name }}" class="w-10 h-10 object-contain"/>--}}
{{--                <span class="text-xs font-medium text-gray-700 text-center">{{ $institution->name }}</span>--}}
{{--            </button>--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--</x-modal>--}}
