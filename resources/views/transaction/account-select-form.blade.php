<x-modal name="account-select" title="Contas" width="max-w-sm sm:max-w-md md:max-w-lg">
    <div class="grid grid-cols-1 gap-3">
        @forelse($accounts as $account)
            <button
                type="button"
                class="w-full p-3 bg-gray-50 hover:bg-blue-50 border-2 border-gray-100 hover:border-blue-400 flex items-center gap-3 rounded-xl transition cursor-pointer"
                x-on:click="
                    const data = {
                        id: '{{ $account->id }}',
                        name: '{{ addslashes($account->name) }}',
                        image: '{{ asset($account->institution->image ?? 'banks-logos/default-bank.svg') }}',
                        institutionName: '{{ addslashes($account->institution->name) }}'
                    };
                    if (accountPickerTarget === 'destination') {
                        selectedDestinationAccount = data;
                    } else {
                        selectedAccount = data;
                    }
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
        @empty
            <div class="flex flex-col items-center justify-center py-8 gap-3 text-center">
                <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="bx bx-wallet text-2xl text-gray-300"></i>
                </div>
                <p class="text-sm text-gray-500 font-medium">Nenhuma conta cadastrada ainda.</p>
            </div>
        @endforelse
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
