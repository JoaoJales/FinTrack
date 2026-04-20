<x-modal name="category-select" title="Categorias" width="md:w-1/4">
    {{-- Despesas --}}
    <div x-show="active === '{{ App\Enums\TransactionType::EXPENSE->value }}'">
        <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Despesas</p>
        <div class="grid grid-cols-1 gap-2">
            @foreach($categoriesByType->get(App\Enums\TransactionType::EXPENSE->value, []) as $category)
                <button
                    type="button"
                    class="w-full p-3 bg-gray-50 hover:bg-red-50 border-2 border-transparent hover:border-red-300 flex items-center gap-3 rounded-xl transition"
                    x-on:click="
                        selectedCategory = {
                            id: '{{ $category->id }}',
                            name: '{{ $category->name }}',
                            icon: '{{ $category->icon ?? 'bx bx-category' }}',
                            color: '{{ $category->color ?? '#5c5e5c' }}'
                        };
                        $dispatch('close-modal', 'category-select')
                    "
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0"
                        style="background-color: {{ $category->color ?? '#5c5e5c' }}"
                    >
                        <i class="{{ $category->icon ?? 'bx bx-category' }} text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800">{{ $category->name }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Receitas --}}
    <div x-show="active === '{{ App\Enums\TransactionType::INCOME->value }}'">
        <p class="text-xs font-semibold text-gray-400 uppercase mb-3">Receitas</p>
        <div class="grid grid-cols-1 gap-2">
            @foreach($categoriesByType->get(App\Enums\TransactionType::INCOME->value, []) as $category)
                <button
                    type="button"
                    class="w-full p-3 bg-gray-50 hover:bg-emerald-50 border-2 border-transparent hover:border-emerald-300 flex items-center gap-3 rounded-xl transition"
                    x-on:click="
                        selectedCategory = {
                            id: '{{ $category->id }}',
                            name: '{{ $category->name }}',
                            icon: '{{ $category->icon ?? 'bx bx-category' }}',
                            color: '{{ $category->color ?? '#5c5e5c' }}'
                        };
                        $dispatch('close-modal', 'category-select')
                    "
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0"
                        style="background-color: {{ $category->color ?? '#5c5e5c' }}"
                    >
                        <i class="{{ $category->icon ?? 'bx bx-category' }} text-white text-xl"></i>
                    </div>
                    <span class="font-medium text-gray-800">{{ $category->name }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="p-4 w-full mt-2 flex items-center justify-center border-t border-gray-100">
        <a href="{{ route('categories.index') }}">
            <x-button-primary class="py-2 px-8 gap-2">
                <i class="bx bx-plus text-3xl"></i>
                <span class="text-lg">Nova categoria</span>
            </x-button-primary>
        </a>
    </div>
</x-modal>
