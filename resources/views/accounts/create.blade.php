<x-modal name="nova-conta" width="md:w-1/3">
    <x-slot name="headerTitle">
        <span x-text="formMethod === 'PUT' ? 'Editar Conta' : 'Nova Conta'"></span>
    </x-slot>

    <form id="form-nova-conta"
          :action="formAction"
          method="POST">
        @csrf
        <input type="hidden" name="_method" x-bind:value="formMethod === 'PUT' ? 'PUT' : ''">

        <div class="grid grid-cols-12 gap-4">

            {{-- Nome da Conta --}}
            <div class="col-span-6">
                <x-form.input
                    name="name"
                    type="text"
                    x-model="accountName"
                    placeholder="Digite o nome da conta"
                    label="Nome da Conta"
                    required/>
            </div>

            {{-- Saldo Inicial --}}
            <div class="col-span-6">
                <div x-bind:class="formMethod === 'PUT' ? 'opacity-50 pointer-events-none' : ''">
                    <x-form.input
                        name="initial_balance"
                        type="text"
                        class="text-right"
                        label="Saldo Inicial (R$)"
                        placeholder="0,00"
                        required
                        x-model="formInitialBalance"
                        x-mask:dynamic="$money($input, ',')"
                    />
                </div>
                <p x-show="formMethod === 'PUT'" class="text-xs text-gray-400 mt-1">
                    O saldo inicial não pode ser alterado após a criação.
                </p>
            </div>

            {{-- Tipo de Conta --}}
            <div class="col-span-12 md:col-span-6">
                <x-form.select name="account_type" label="Tipo de Conta" x-model="accountType" required>
                    <option value="">Selecione o tipo</option>
                    @foreach(App\Enums\AccountType::cases() as $type)
                        <x-form.select-option value="{{ $type->value }}">
                            {{ $type->label() }}
                        </x-form.select-option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Conta Principal --}}
            <div class="col-span-12 md:col-span-6" x-show="accountsCount >= 1">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 h-full"
                     :class="editingIsDefault ? 'opacity-60' : ''">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-700">É minha conta principal?</span>
                        <span class="text-xs text-gray-500" x-show="!editingIsDefault">Selecionada por padrão em novas transações.</span>
                        <span class="text-xs text-emerald-600 font-medium" x-show="editingIsDefault">✓ Esta já é sua conta principal.</span>
                    </div>
                    <label class="relative inline-flex items-center"
                           :class="editingIsDefault ? 'cursor-not-allowed' : 'cursor-pointer'">
                        <input type="checkbox"
                               name="is_default"
                               id="is_default"
                               value="1"
                               class="sr-only peer"
                               x-model="isDefault"
                               :disabled="editingIsDefault">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            {{-- Banco selecionado  --}}
            <div class="col-span-12">
                <p class="text-xs mb-1 text-slate-600 font-medium">
                    Banco <span class="text-red-600 text-xs">*</span>
                </p>
                <input type="hidden" name="institution_id" :value="selectedInstitution.id">
                <div
                    class="w-full p-3 bg-gray-100 flex items-center gap-2 rounded-xl shadow-md cursor-pointer hover:bg-gray-200 transition"
                    x-on:click="$dispatch('open-modal', 'institution-select')"
                >
                    <div class="w-10 h-10 p-1 rounded-xl flex items-center justify-center shadow-sm bg-white">
                        <img :src="selectedInstitution.image" :alt="selectedInstitution.name" class="w-8 h-8 object-contain" alt=""/>
                    </div>
                    <div class="flex items-center justify-between w-full">
                        <span class="text-base px-2 font-medium" x-text="selectedInstitution.name"></span>
                        <i class="bx bx-chevron-right text-3xl text-blue-500"></i>
                    </div>
                </div>
            </div>

            {{-- Deletar conta (somente na edição) --}}
            <div class="col-span-12" x-show="formMethod === 'PUT'">
                <template x-if="editingIsDefault && accountsCount > 1">
                    <div class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-100 bg-red-50 text-red-300 text-sm font-medium cursor-not-allowed"
                         title="Não é possível excluir a conta principal">
                        <i class="bx bx-trash text-lg"></i>
                        Excluir esta conta
                    </div>
                </template>

                <template x-if="!editingIsDefault">
                    <button
                        type="button"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition text-sm font-medium"
                        x-on:click="$dispatch('confirm-dialog', {
                            title: 'Excluir conta',
                            message: 'Tem certeza que deseja excluir esta conta? Esta ação não pode ser desfeita.',
                            confirmLabel: 'Sim, excluir',
                            confirmType: 'danger',
                            onConfirm: () => $refs.formDeleteAccount.submit()
                        })"
                    >
                        <i class="bx bx-trash text-lg"></i>
                        Excluir esta conta
                    </button>
                </template>
            </div>

        </div>
    </form>

    <form x-ref="formDeleteAccount"
          :action="`/accounts/${formAction.split('/').pop()}`"
          method="POST"
          class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <x-slot name="footer">
        <x-button-cancel type="button" @click="$dispatch('close-modal', 'nova-conta')">
            <i class="bx bx-x text-xl"></i> Cancelar
        </x-button-cancel>
        <x-button-confirm type="submit" form="form-nova-conta" class="gap-2">
            <i class="bx bx-check text-xl"></i>
            <span x-text="formMethod === 'PUT' ? 'Atualizar Conta' : 'Salvar Conta'"></span>
        </x-button-confirm>
    </x-slot>
</x-modal>
