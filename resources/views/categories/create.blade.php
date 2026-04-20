<x-modal name="nova-categoria" width="md:w-1/3">
    <x-slot name="headerTitle">Nova Categoria</x-slot>

    <div x-data="{
            name: '',
            type: '{{ App\Enums\TransactionType::EXPENSE->value }}',
            icon: 'bx-tag',
            color: '#6366f1',
            icons: [
                'bx-tag','bx-home','bx-car','bx-food-menu','bx-cart','bx-heart',
                'bx-book','bx-game','bx-briefcase','bx-plane','bx-credit-card',
                'bx-gift','bx-music','bx-dumbbell','bx-store','bx-coffee',
                'bx-mobile','bx-devices','bx-dollar-circle','bx-trending-up',
            ],
            colors: [
                '#6366f1','#ec4899','#f97316','#eab308','#22c55e',
                '#14b8a6','#3b82f6','#a855f7','#ef4444','#64748b',
            ],
        }"
         x-on:open-modal.window="
            if ($event.detail === 'nova-categoria') {
                name  = '';
                type  = '{{ App\Enums\TransactionType::EXPENSE->value }}';
                icon  = 'bx-tag';
                color = '#6366f1';
            }
         "
    >
        <form method="POST"
              id="form-nova-categoria"
              action="{{ route('categories.store') }}">
            @csrf

            <div class="px-6 py-5 space-y-5">

                {{-- Preview --}}
                <div class="flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-3xl shadow-sm transition-all duration-200"
                             :style="`background-color: ${color};`">
                            <i class="bx text-white" :class="icon"></i>
                        </div>
                        <span class="text-xs text-gray-400 font-medium" x-text="name || 'Pré-visualização'"></span>
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nome <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        x-model="name"
                        placeholder="Ex: Alimentação, Salário..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition"
                        required
                        autofocus
                    >
                    @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type"
                                   value="{{ App\Enums\TransactionType::EXPENSE->value }}"
                                   x-model="type" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border-2 border-gray-200
                                        peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:text-red-600
                                        text-gray-500 text-sm font-medium transition-all duration-150 hover:border-gray-300">
                                <i class="bx bx-trending-down text-lg"></i>
                                Gasto
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type"
                                   value="{{ App\Enums\TransactionType::INCOME->value }}"
                                   x-model="type" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border-2 border-gray-200
                                        peer-checked:border-emerald-400 peer-checked:bg-emerald-50 peer-checked:text-emerald-600
                                        text-gray-500 text-sm font-medium transition-all duration-150 hover:border-gray-300">
                                <i class="bx bx-trending-up text-lg"></i>
                                Ganho
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Icon picker --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ícone</label>
                    <input type="hidden" name="icon" :value="'bx ' + icon">
                    <div class="grid grid-cols-10 gap-1.5">
                        <template x-for="ic in icons" :key="ic">
                            <button
                                type="button"
                                x-on:click="icon = ic"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-base transition-all duration-100"
                                :class="icon === ic ? 'text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                :style="icon === ic ? `background-color: ${color};` : ''"
                                :title="ic"
                            >
                                <i class="bx" :class="ic"></i>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Color picker --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cor</label>
                    <input type="hidden" name="color" :value="color">
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="c in colors" :key="c">
                            <button
                                type="button"
                                x-on:click="color = c"
                                class="w-7 h-7 rounded-full transition-all duration-100 border-2"
                                :style="`background-color: ${c}; border-color: ${color === c ? c : 'transparent'}; outline: ${color === c ? '2px solid ' + c + '44' : 'none'}; outline-offset: 2px;`"
                                :title="c"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button-cancel type="button" @click="$dispatch('close-modal', 'nova-categoria')">
                <i class="bx bx-x text-xl"></i> Cancelar
            </x-button-cancel>
            <x-button-confirm type="submit" form="form-nova-categoria" class="gap-2">
                <i class="bx bx-check text-xl"></i> Salvar Categoria
            </x-button-confirm>
        </x-slot>
    </div>
</x-modal>
