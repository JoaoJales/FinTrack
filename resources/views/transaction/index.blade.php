<x-app-layout>
    <div x-data='{
        filters: { search: "", category: "", type: "", start_date: "", end_date: "" },
        selectedAccount: @json($defaultAccountData),
        selectedCategory: @json($defaultExpenseCategoryData),
        defaultExpenseCategory: @json($defaultExpenseCategory),
        defaultIncomeCategory: @json($defaultIncomeCategory),
        active: "{{ App\Enums\TransactionType::EXPENSE->value }}",

        transactionDate: "{{ now()->format('d/m/Y') }}",
        formAmount: "",
        formDescription: "",
        formMethod: "POST",
        formAction: "{{ route('transactions.store') }}",

        resetForm() {
            const form = document.getElementById("form-nova-transacao");
            if (form) form.reset();

            this.active = "{{ App\Enums\TransactionType::EXPENSE->value }}";
            this.selectedAccount = @json($defaultAccountData);
            this.selectedCategory = @json($defaultExpenseCategoryData);
            this.formMethod = "POST";
            this.formAction = "{{ route('transactions.store') }}";
            this.formAmount = "";
            this.formDescription = "";

            this.$nextTick(() => {
                this.transactionDate = "{{ now()->format('d/m/Y')  }}";
            });
        },

        openEditModal(transaction) {
            this.formMethod = "PUT";
            this.formAction = `/transactions/${transaction.id}`;
            this.active = transaction.type;
            this.formAmount = transaction.amount;
            this.formDescription = transaction.description;
            this.transactionDate = transaction.date;
            this.selectedAccount = transaction.account;
            this.selectedCategory = transaction.category;

            this.$dispatch("open-modal", "nova-transacao");
        }
     }'
         x-on:open-modal.window="if ($event.detail === 'nova-transacao') { /* não reseta ao editar */ }"
         x-on:open-edit-transaction.window="openEditModal($event.detail)"
    >

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Extrato</h1>
                <p class="text-gray-500 mt-1">Gerencie suas receitas e despesas</p>
            </div>
{{--            <a href="{{ route('transactions.create') }}">--}}
                <x-button-primary class="py-2 px-5 gap-2" x-on:click="resetForm(); $dispatch('open-modal', 'nova-transacao')">
                    <i class="bx bx-plus text-xl"></i>
                    <span class="text-base">Nova Transação</span>
                </x-button-primary>
{{--            </a>--}}
        </div>

        <div class="p-5 rounded-2xl border border-gray-100 shadow-lg mb-6 bg-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-gray-800">Filtros</h4>
                <x-button-outline
                    @click="filters = { search: '', category: '', type: '', start_date: '', end_date: '' }; window.location.href='{{ route('transactions.index') }}'">
                    <i class="bx bx-eraser"></i> Limpar
                </x-button-outline>
            </div>

            <form action="{{ route('transactions.index') }}" method="GET">
                <div class="grid grid-cols-12 gap-4 items-end">
                    <div class="col-span-2">
                        <x-form.input name="search" label="Buscar transação" placeholder="Ex: Supermercado..." x-model="filters.search"/>
                    </div>

                    <div class="col-span-2">
                        <x-form.select name="type" label="Tipo" x-model="filters.type">
                            <option value="" class="text-slate-50">Todos os tipos</option>
                            <x-form.select-option value="income">Entrada (+)</x-form.select-option>
                            <x-form.select-option value="expense">Saída (-)</x-form.select-option>
                        </x-form.select>
                    </div>

                    <div class="col-span-2">
                        <x-form.select name="category_id" label="Categoria" x-model="filters.category">
                            <option value="">Todas categorias</option>
                             @foreach($categories as $cat)
                                <x-form.select-option value="{{ $cat->id }}">{{ $cat->name }}</x-form.select-option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <div class="col-span-2">
                        <x-form.input name="date_start" type="date" label="Data Início" x-model="filters.start_date"/>
                    </div>

                    <div class="col-span-2">
                        <x-form.input name="date_end" type="date" label="Data Fim" x-model="filters.end_date" />
                    </div>

                    <div class="flex items-center gap-2 col-span-2 justify-end">
                        <x-button-primary type="submit" class="px-3 py-1 gap-2">
                            <i class="bx bx-filter text-lg"></i>
                            <span>Filtrar</span>
                        </x-button-primary>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transações -->
        <div class="">
            <div class="flex justify-between items-center mb-6">
                <span class="text-lg font-bold text-gray-800">Transações</span>
            </div>

            <x-table-scroll>
                <x-table>
                    <x-table.thead>
                        <x-table.header-row class="border-b border-gray-200">
                            <x-table.header-col>Data</x-table.header-col>
                            <x-table.header-col class="text-right">Valor</x-table.header-col>
                            <x-table.header-col class="text-center">Categoria</x-table.header-col>
                            <x-table.header-col class="text-center">Conta</x-table.header-col>
                            <x-table.header-col>Descrição</x-table.header-col>
                            <x-table.header-col class="text-right">Ações</x-table.header-col>
                        </x-table.header-row>
                    </x-table.thead>

                    <x-table.tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $transaction)
                            <x-table.row>
                                <x-table.col class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </x-table.col>

                                <x-table.col class="text-right whitespace-nowrap font-bold {{ $transaction->category?->type?->color() }}">
                                    <span class="text-sm font-semibold tabular-nums">
                                        {{ $transaction->category->type === \App\Enums\TransactionType::INCOME ? '+' : '-' }}
                                        R$ @moneyBr($transaction->amount)
                                    </span>
                                </x-table.col>

                                <x-table.col class="text-center align-middle">
                                    <div class="flex justify-center">
                                        @if($transaction->category)
                                            <div class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-full w-fit"
                                                 style="background-color: {{ $transaction->category->color }}20">
                                                <!-- Ícone -->
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center"
                                                     style="background-color: {{ $transaction->category->color }}">
                                                    <i class="{{ $transaction->category->icon }} text-white text-sm"></i>
                                                </div>

                                                <!-- Nome -->
                                                <span class="text-xs font-medium text-gray-700">
                                                    {{ $transaction->category->name }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sem categoria</span>
                                        @endif
                                    </div>
                                </x-table.col>

                                <x-table.col class="text-center align-middle">
                                    <div class="flex justify-center">
                                        <div class="flex items-center gap-2 pr-2.5 py-1 rounded-full bg-gray-100 w-fit">
                                            <!-- Logo do banco -->
                                            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-white shadow-sm">
                                                <x-institution-logo :image="$transaction->account->institution->image" :name="$transaction->account->institution->name" size="w-4 h-4"/>
                                            </div>

                                            <!-- Nome da conta -->
                                            <span class="text-xs font-medium text-gray-700">
                                                {{ $transaction->account->name }}
                                            </span>
                                        </div>
                                    </div>
                                </x-table.col>

                                <x-table.col>
                                    <span class="text-sm font-medium text-gray-900 truncate max-w-[250px] block">
                                        {{ $transaction->description }}
                                    </span>
                                </x-table.col>

                                <x-table.col class="text-sm font-medium text-right">
                                    <div class="flex items-center justify-end">
                                        <button
                                            type="button"
                                            title="Editar"
                                            class="text-slate-500 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition"
                                            x-on:click="openEditModal({
                                                id: {{ $transaction->id }},
                                                type: '{{ $transaction->category->type->value }}',
                                                amount: '{{ number_format($transaction->amount, 2, ',', '.') }}',
                                                description: '{{ addslashes($transaction->description) }}',
                                                date: '{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}',
                                                account: {
                                                    id: {{ $transaction->account->id }},
                                                    name: '{{ addslashes($transaction->account->name) }}',
                                                    image: '{{ $transaction->account->institution->image }}'
                                                },
                                                category: {
                                                    id: {{ $transaction->category->id }},
                                                    name: '{{ addslashes($transaction->category->name) }}',
                                                    icon: '{{ $transaction->category->icon }}',
                                                    color: '{{ $transaction->category->color }}'
                                                }
                                            })"
                                        >
                                            <i class="bx bx-edit text-xl"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="text-slate-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition"
                                            title="Excluir"
                                            x-on:click="$dispatch('confirm-dialog', {
                                                title: 'Excluir transação',
                                                message: 'Tem certeza que deseja excluir esta transação? Esta ação não pode ser desfeita.',
                                                confirmLabel: 'Sim, excluir',
                                                confirmType: 'danger',
                                                onConfirm: () => $refs.formDeleteTransaction{{ $transaction->id }}.submit()
                                            })"
                                            >
                                                <i class="bx bx-trash text-xl"></i>
                                        </button>

                                        <form x-ref="formDeleteTransaction{{ $transaction->id }}"
                                              action="{{ route('transactions.destroy', $transaction->id) }}"
                                              method="POST"
                                              class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </x-table.col>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.col colspan="7" class="py-8 text-center text-sm text-gray-400 border-gray-200">
                                    Nenhuma transação encontrada.
                                </x-table.col>
                            </x-table.row>
                        @endforelse
                    </x-table.tbody>
                </x-table>
            </x-table-scroll>

            <!-- Paginação -->
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
        @include('transaction.create')
        @include('transaction.account-select-form')
        @include('transaction.category-select-form')
    </div>
</x-app-layout>
