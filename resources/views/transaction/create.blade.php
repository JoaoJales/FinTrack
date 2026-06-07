<x-modal name="nova-transacao" width="max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl">
    <x-slot name="headerTitle">
        <span x-text="formMethod === 'PUT' ? 'Editar Transação' : 'Nova Transação'"></span>
    </x-slot>

    <form id="form-nova-transacao"
          :action="formAction"
          method="POST">
        @csrf
        <input type="hidden" name="_method" x-bind:value="formMethod === 'PUT' ? 'PUT' : ''">

        <div x-init="$watch('active', type => {
                 if (type === '{{ App\Enums\TransactionType::TRANSFER->value }}') return;
                 let cat = type === '{{ App\Enums\TransactionType::EXPENSE->value }}' ? defaultExpenseCategory : defaultIncomeCategory;
                 if (cat) {
                     selectedCategory = { id: cat.id, name: cat.name, icon: cat.icon ?? 'bx bx-category', color: cat.color ?? '#5c5e5c'};
                 }
            })
        ">
            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 mb-6">
                <x-tabs-link color="red" :value="App\Enums\TransactionType::EXPENSE->value">Gasto</x-tabs-link>
                <x-tabs-link color="emerald" :value="App\Enums\TransactionType::INCOME->value">Ganho</x-tabs-link>
                <x-tabs-link
                    color="blue"
                    :value="App\Enums\TransactionType::TRANSFER->value"
                    x-bind:disabled="!canTransfer"
                >Transferência</x-tabs-link>
            </div>

            <input type="hidden" name="type" :value="active">

            <div class="grid grid-cols-12 gap-4">

                {{-- Valor --}}
                <div class="col-span-6">
                    <x-form.input name="amount" type="text" class="text-right" label="Valor (R$)" placeholder="0,00" required
                                  x-model="formAmount"
                                  x-mask:dynamic="$money($input, ',')"/>
                </div>

                {{-- Data --}}
                <div class="col-span-6">
                    <x-form.input name="date" x-model="transactionDate" x-mask="99/99/9999" placeholder="dd/mm/aaaa" label="Data" required/>
                </div>

                {{-- Conta origem --}}
                <div class="col-span-12">
                    <p class="text-xs mb-1 text-slate-600 font-medium">
                        <span x-text="active === '{{ App\Enums\TransactionType::TRANSFER->value }}' ? 'Conta de origem' : 'Conta'"></span>
                        <span class="text-red-600 text-xs">*</span>
                    </p>
                    <input type="hidden" name="account_id" :value="selectedAccount.id">
                    <div
                        class="w-full p-3 bg-gray-100 flex items-center gap-2 rounded-xl shadow-md cursor-pointer hover:bg-gray-200 transition"
                        x-on:click="accountPickerTarget = 'origin'; $dispatch('open-modal', 'account-select')"
                    >
                        <div class="w-10 h-10 p-1 rounded-xl flex items-center justify-center shadow-sm bg-white">
                            <template x-if="selectedAccount.id">
                                <img :src="selectedAccount.image" :alt="selectedAccount.name" class="w-8 h-8 object-contain"/>
                            </template>
                            <template x-if="!selectedAccount.id">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="bx bx-wallet text-gray-400 text-xl"></i>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-between w-full">
                            <div>
                                <span class="text-base px-2 font-medium" :class="!selectedAccount.id ? 'text-gray-400' : ''" x-text="selectedAccount.name"></span>
                            </div>
                            <i class="bx bx-chevron-right text-3xl text-blue-500"></i>
                        </div>
                    </div>
                </div>

                {{-- Conta destino --}}
                <div class="col-span-12" x-show="active === '{{ App\Enums\TransactionType::TRANSFER->value }}'" x-cloak>
                    <p class="text-xs mb-1 text-slate-600 font-medium">
                        Conta de destino <span class="text-red-600 text-xs">*</span>
                    </p>
                    <input type="hidden" name="destination_account_id" :value="selectedDestinationAccount.id" x-bind:disabled="active !== '{{ App\Enums\TransactionType::TRANSFER->value }}'">
                    <div
                        class="w-full p-3 bg-gray-100 flex items-center gap-2 rounded-xl shadow-md cursor-pointer hover:bg-gray-200 transition"
                        x-on:click="accountPickerTarget = 'destination'; $dispatch('open-modal', 'account-select')"
                    >
                        <div class="w-10 h-10 p-1 rounded-xl flex items-center justify-center shadow-sm bg-white">
                            <template x-if="selectedDestinationAccount.id">
                                <img :src="selectedDestinationAccount.image" :alt="selectedDestinationAccount.name" class="w-8 h-8 object-contain"/>
                            </template>
                            <template x-if="!selectedDestinationAccount.id">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="bx bx-wallet text-gray-400 text-xl"></i>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-between w-full">
                            <div>
                                <span class="text-base px-2 font-medium" :class="!selectedDestinationAccount.id ? 'text-gray-400' : ''" x-text="selectedDestinationAccount.name"></span>
                            </div>
                            <i class="bx bx-chevron-right text-3xl text-blue-500"></i>
                        </div>
                    </div>
                </div>

                {{-- Categoria --}}
                <div class="col-span-12" x-show="active !== '{{ App\Enums\TransactionType::TRANSFER->value }}'">
                    <p class="text-xs mb-1 text-slate-600 font-medium">
                        Categoria <span class="text-red-600 text-xs">*</span>
                    </p>
                    <input type="hidden" name="category_id" :value="selectedCategory.id" x-bind:disabled="active === '{{ App\Enums\TransactionType::TRANSFER->value }}'">
                    <div
                        class="w-full p-3 bg-gray-100 flex items-center gap-2 rounded-xl shadow-md cursor-pointer hover:bg-gray-200 transition"
                        x-on:click="$dispatch('open-modal', 'category-select')"
                    >
                        <div
                            class="w-10 h-10 p-1 rounded-xl flex items-center justify-center shadow-sm"
                            :style="'background-color: ' + selectedCategory.color"
                        >
                            <i :class="selectedCategory.icon + ' text-white text-xl'"></i>
                        </div>
                        <div class="flex items-center justify-between w-full">
                            <span class="text-base px-2 font-medium" x-text="selectedCategory.name"></span>
                            <i class="bx bx-chevron-right text-3xl text-blue-500"></i>
                        </div>
                    </div>
                </div>

                {{-- Descrição --}}
                <div class="col-span-12">
                    <x-form.textarea
                        name="description"
                        label="Descrição"
                        placeholder="Ex: Compra no supermercado, Salário..."
                        x-model="formDescription"
                    />
                </div>
            </div>
        </div>
    </form>

    {{-- Footer --}}
    <x-slot name="footer">
        <x-button-cancel type="button" @click="$dispatch('close-modal', 'nova-transacao')">
            <i class="bx bx-x text-xl"></i> Cancelar
        </x-button-cancel>

        <x-button-confirm type="submit" form="form-nova-transacao" class="gap-2">
            <i class="bx bx-check text-xl"></i>
            <span x-text="formMethod === 'PUT' ? 'Atualizar Transação' : 'Salvar Transação'"></span>
        </x-button-confirm>
    </x-slot>
</x-modal>
