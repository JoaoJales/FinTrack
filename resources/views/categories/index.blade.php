<x-app-layout>
    <div class="max-w-4xl mx-auto" x-data="{
        active: '{{ request('type', App\Enums\TransactionType::EXPENSE->value) }}'
    }"
         x-on:tab-changed.window="
            const url = new URL(window.location.href);
            url.searchParams.set('type', active);
            window.location.href = url.toString();
         "
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Categorias</h1>
                <p class="text-gray-500 mt-1">Gerencie e personalize suas categorias</p>
            </div>
            <x-button-primary class="py-2 px-5 gap-2" x-on:click="$dispatch('open-modal', 'nova-categoria')">
                <i class="bx bx-plus text-xl"></i>
                <span class="text-base">Nova Categoria</span>
            </x-button-primary>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200">
            <x-tabs-link color="red"     :value="App\Enums\TransactionType::EXPENSE->value"
                         x-on:click="$nextTick(() => $dispatch('tab-changed'))">Gasto</x-tabs-link>
            <x-tabs-link color="emerald" :value="App\Enums\TransactionType::INCOME->value"
                         x-on:click="$nextTick(() => $dispatch('tab-changed'))">Ganho</x-tabs-link>
        </div>

        <x-card class="rounded-t-none rounded-b-2xl">
            <div class="space-y-2">
                @forelse($categories as $category)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition-all duration-150 group">

                        {{-- Icon + Info --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                                 style="background-color: {{ $category->color ?? '#6366f1' }}">
                                <i class="bx {{ $category->icon ?? 'bx-tag' }} text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm leading-tight">{{ $category->name }}</p>
                                @if($category->type === App\Enums\TransactionType::EXPENSE)
                                    <span class="inline-flex items-center gap-1 text-xs text-red-500 font-medium mt-0.5">
                                        <i class="bx bx-trending-down text-sm"></i> Gasto
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-500 font-medium mt-0.5">
                                        <i class="bx bx-trending-up text-sm"></i> Ganho
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                            <button
                                class="p-1 rounded-lg hover:bg-blue-100 text-slate-500 hover:text-blue-500 transition-colors"
                                x-on:click="$dispatch('open-modal', 'editar-categoria-{{ $category->id }}')"
                                title="Editar"
                            >
                                <i class="bx bx-edit text-xl"></i>
                            </button>
                            <button
                                type="button"
                                class="text-slate-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition"
                                title="Excluir"
                                x-on:click="$dispatch('confirm-dialog', {
                                    title: 'Excluir Categoria',
                                    message: 'Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.',
                                    confirmLabel: 'Sim, excluir',
                                    confirmType: 'danger',
                                    onConfirm: () => $refs.formDeleteCategory{{ $category->id }}.submit()
                                })"
                            >
                                <i class="bx bx-trash text-xl"></i>
                            </button>

                            <form x-ref="formDeleteCategory{{ $category->id }}"
                                  action="{{ route('categories.destroy', $category->id) }}"
                                  method="POST"
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>

                    @include('categories.edit', ['category' => $category])

                @empty
                    <div class="flex flex-col items-center justify-center py-12 gap-3 text-center">
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl text-gray-300">
                            <i class="bx bx-category"></i>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Nenhuma categoria personalizada cadastrada.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

        @include('categories.create')
    </div>
</x-app-layout>
