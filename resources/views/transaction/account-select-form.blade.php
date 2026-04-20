<x-modal name="account-select" title="Contas" width="md:w-1/4">
    <div class="grid grid-cols-1 gap-3">
        @foreach($accounts as $account)
            <button
                type="button"
                class="w-full p-3 bg-gray-50 hover:bg-blue-50 border-2 border-gray-100 hover:border-blue-400 flex items-center gap-3 rounded-xl transition cursor-pointer"
                x-on:click="
                    selectedAccount = {
                        id: '{{ $account->id }}',
                        name: '{{ $account->name }}',
                        image: '{{ $account->institution->image }}',
                        institutionName: '{{ $account->institution->name }}'
                    };
                    $dispatch('close-modal', 'account-select')
                "
            >
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                    <x-institution-logo :image="$account->institution->image" :alt="$account->institution->name"/>
                </div>
                <div class="flex items-center justify-between w-full">
                    <div><p class="font-semibold text-gray-800">{{ $account->name }}</p></div>
                    <div><p class="font-bold text-gray-900 text-sm">R$ @moneyBr($account->current_balance)</p></div>
                </div>
            </button>
        @endforeach
    </div>

    <div class="p-4 w-full mt-2 flex items-center justify-center border-t border-gray-100">
            <a href="{{ route('accounts.index') }}">
                <x-button-primary class="py-2 px-8 gap-2">
                    <i class="bx bx-plus text-3xl"></i>
                    <span class="text-lg">Criar Conta</span>
                </x-button-primary>
            </a>
    </div>
</x-modal>
