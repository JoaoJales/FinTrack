<x-app-layout>
    <div class="max-w-4xl mx-auto" x-data='{
        selectedInstitution: { id: null, name: "Selecione um banco", image: "/banks-logos/default-bank.png" },
        accountName: "",
        formInitialBalance: "",
        isDefault: false,
        accountType: "",
        accountsCount: {{ $accountsCount }},
        editingIsDefault: false,
        formMethod: "POST",
        formAction: "{{ route('accounts.store') }}",

        resetForm() {
            this.selectedInstitution = { id: null, name: "Selecione um banco", image: "/banks-logos/default-bank.png" };
            this.accountName = "";
            this.formInitialBalance = "";
            this.accountType = "";
            this.isDefault = false;
            this.editingIsDefault = false;
            this.formMethod = "POST";
            this.formAction = "{{ route('accounts.store') }}";
        },

        openEditModal(account) {
            this.formMethod = "PUT";
            this.formAction = `/accounts/${account.id}`;
            this.accountName = account.name;
            this.formInitialBalance = account.initial_balance;
            this.accountType = account.account_type;
            this.isDefault = account.is_default;
            this.editingIsDefault = account.is_default;
            this.selectedInstitution = account.institution;
            this.$dispatch("open-modal", "nova-conta");
        }
    }'
         x-on:institution-selected.window="accountName = $event.detail.name;
         $dispatch('open-modal', 'nova-conta');
        "
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Contas</h1>
                <p class="text-gray-500 mt-1">Gerencie suas Contas Bancárias</p>
            </div>
            <x-button-primary class="py-2 px-5 gap-2" x-on:click="resetForm(); $dispatch('open-modal', 'institution-select');">
                <i class="bx bx-plus text-xl"></i>
                <span class="text-base">Nova Conta</span>
            </x-button-primary>
        </div>

        <x-card>
            <div class="mb-6">
                <p class="text-sm text-gray-400">Saldo total</p>
                <h2 class="text-2xl font-bold">
                    R$ @moneyBr($total_balance)
                </h2>
            </div>

            <div class="space-y-4">
                @forelse($accounts as $account)

                    <div
                        class="cursor-pointer"
                        x-on:click="openEditModal({
                            id: {{ $account->id }},
                            name: '{{ addslashes($account->name) }}',
                            initial_balance: '@moneyBr($account->initial_balance)',
                            account_type: '{{ $account->account_type->value }}',
                            is_default: {{ $account->is_default ? 'true' : 'false' }},
                            institution: {
                                id: {{ $account->institution->id }},
                                name: '{{ addslashes($account->institution->name) }}',
                                image: '{{ $account->institution->image }}'
                            }
                        })"
                    >
                        <x-bank-account-item :account="$account"/>
                    </div>

                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Nenhuma conta cadastrada.</p>
                @endforelse
            </div>
        </x-card>

        @include('accounts.create')
        @include('accounts.institution-select-form')
    </div>
</x-app-layout>
