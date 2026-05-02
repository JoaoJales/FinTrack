<x-modal name="institution-select" title="Selecione o Banco" width="md:w-1/4">
    <div class="grid grid-cols-1 gap-3">
        @foreach($institutions as $institution)
            @continue(str_contains($institution->image, 'default-bank.svg') || empty($institution->image))
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
