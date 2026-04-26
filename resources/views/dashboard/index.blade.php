<x-app-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Visão geral das suas finanças • {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <a href="{{ route('transactions.index') }}">
            <x-button-primary class="py-2 px-5 gap-2">
                <span class="text-base">Ver Transações</span>
                <i class="bx bxs-right-arrow-alt text-xl"></i>
            </x-button-primary>
        </a>
    </div>


    <!-- Grid Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Coluna Esquerda (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Cards de Resumo -->
            <div class="gap-4">
                <!-- Saldo Total -->
                <x-card class="overflow-hidden flex justify-between">
                    <div class="">
                        <p class="text-sm font-medium text-gray-500 mb-1">Saldo Total</p>
                        <h3 class="text-3xl font-bold text-gray-900">R$ @moneyBr($total_balance)</h3>
                        <div class="flex items-center mt-2 font-medium
                            {{ $balance_variation['positive'] ? 'text-emerald-600' : 'text-rose-600' }}">

                            @if($balance_variation['percentage'] !== null)
                                <i class="bx {{ $balance_variation['positive'] ? 'bx-trending-up' : 'bx-trending-down' }} mr-1"></i>
                                {{ $balance_variation['positive'] ? '+' : '-' }}{{ $balance_variation['percentage'] }}% Vs {{ now()->subMonth()->translatedFormat('F') }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <x-link href="#">Ver detalhes</x-link>
                    </div>
                </x-card>
            </div>


            <!-- Últimas Transações -->
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <span class="text-lg font-bold text-gray-800">Últimas Transações</span>
                    <x-link href="{{ route('transactions.index') }}">Ver todas →</x-link>
                </div>

                <x-table-scroll>
                    <x-table>
                        <x-table.thead>
                            <x-table.header-row class="border-b border-gray-100">
                                <x-table.header-col>Data</x-table.header-col>
                                <x-table.header-col class="text-center">Categoria</x-table.header-col>
                                <x-table.header-col class="text-center">Conta</x-table.header-col>
                                <x-table.header-col>Descrição</x-table.header-col>
                                <x-table.header-col class="text-right">Valor</x-table.header-col>
                            </x-table.header-row>
                        </x-table.thead>

                        <x-table.tbody class="divide-y divide-gray-100">
                            @forelse($last_transactions as $transaction)
                                <x-table.row>
                                    <x-table.col class="py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
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
                                            <div class="flex items-center gap-2 pr-2.5 py-1 rounded-full w-fit">
                                                <!-- Logo do banco -->
                                                <div class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-50 shadow-md">
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

                                    <x-table.col class="text-right whitespace-nowrap font-bold {{ $transaction->category?->type?->color() }}">
                                        <span class="text-sm font-semibold tabular-nums">
                                            {{ $transaction->category->type === \App\Enums\TransactionType::INCOME ? '+' : '-' }}
                                            R$ @moneyBr($transaction->amount)
                                        </span>
                                    </x-table.col>
                                </x-table.row>
                                @empty
                                    <x-table.row>
                                        <x-table.col colspan="4" class="py-8 text-center text-sm text-gray-400">
                                            Nenhuma transação encontrada.
                                        </x-table.col>
                                    </x-table.row>
                                @endforelse
                        </x-table.tbody>
                    </x-table>
                </x-table-scroll>
            </x-card>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Gastos/Ganhos por Categoria --}}
                <x-card class="w-full" x-data="{tab: 'expense'}">
                    @php
                        // Expenses
                        $totalExpenses      = $expenses_by_category->sum('total');
                        $expenseLabels      = $expenses_by_category->pluck('name')->toArray();
                        $expenseColors      = $expenses_by_category->pluck('color')->toArray();
                        $expenseAmounts     = $expenses_by_category->pluck('total')->toArray();
                        $expensePercentages = $expenses_by_category->map(fn($c) => $totalExpenses > 0 ? round(($c->total / $totalExpenses) * 100) : 0)->toArray();

                        if (empty($expenseLabels)) {
                            $expenseLabels      = ['Sem dados'];
                            $expenseColors      = ['#e5e7eb'];
                            $expenseAmounts     = [0];
                            $expensePercentages = [100];
                        }

                        // Incomes
                        $totalIncomes      = $incomes_by_category->sum('total');
                        $incomeLabels      = $incomes_by_category->pluck('name')->toArray();
                        $incomeColors      = $incomes_by_category->pluck('color')->toArray();
                        $incomeAmounts     = $incomes_by_category->pluck('total')->toArray();
                        $incomePercentages = $incomes_by_category->map(fn($c) => $totalIncomes > 0 ? round(($c->total / $totalIncomes) * 100) : 0)->toArray();

                        if (empty($incomeLabels)) {
                            $incomeLabels      = ['Sem dados'];
                            $incomeColors      = ['#e5e7eb'];
                            $incomeAmounts     = [0];
                            $incomePercentages = [100];
                        }
                    @endphp

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800" x-text="tab === 'expense' ? 'Gastos por categoria' : 'Ganhos por categoria'"></h2>
                            <x-link href="{{ route('categories.index') }}">Ver minhas categorias</x-link>
                        </div>
                        <div class="flex bg-gray-100 rounded-lg p-1 gap-1">
                            <button
                                class="p-1.5 rounded-md transition-colors"
                                :class="tab === 'expense' ? 'bg-white shadow-sm text-rose-500' : 'text-gray-400 hover:text-gray-600'"
                                x-on:click="tab = 'expense'"
                                title="Gastos"
                            >
                                <i class="bx bx-trending-down"></i>
                            </button>
                            <button
                                class="p-1.5 rounded-md transition-colors"
                                :class="tab === 'income' ? 'bg-white shadow-sm text-emerald-500' : 'text-gray-400 hover:text-gray-600'"
                                x-on:click="tab = 'income'"
                                title="Ganhos"
                            >
                                <i class="bx bx-trending-up"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex-1 flex flex-col justify-between">
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">
                                    <span x-show="tab === 'expense'">Total de gastos</span>
                                    <span x-show="tab === 'income'">Total de ganhos</span>
                                </p>
                                <p x-show="tab === 'expense'" class="text-2xl font-bold text-rose-600">
                                    R$ @moneyBr($totalExpenses)
                                </p>
                                <p x-show="tab === 'income'" class="text-2xl font-bold text-emerald-600">
                                    R$ @moneyBr($totalIncomes)
                                </p>
                            </div>

                            {{-- Legenda Gastos --}}
                            <div x-show="tab === 'expense'" class="space-y-3 min-h-[150px]">
                                @forelse($expenses_by_category as $category)
                                    @php $pct = $totalExpenses > 0 ? round(($category->total / $totalExpenses) * 100) : 0; @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $category->color ?? '#e5e7eb' }}"></div>
                                            <span class="text-sm text-gray-700">{{ $category->name }}</span>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">({{ $pct }}%)</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400 mt-4">Sem gastos registrados este mês.</p>
                                @endforelse
                            </div>

                            {{-- Legenda Ganhos --}}
                            <div x-show="tab === 'income'" class="space-y-3 min-h-[150px]">
                                @forelse($incomes_by_category as $category)
                                    @php $pct = $totalIncomes > 0 ? round(($category->total / $totalIncomes) * 100) : 0; @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $category->color ?? '#e5e7eb' }}"></div>
                                            <span class="text-sm text-gray-700">{{ $category->name }}</span>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">({{ $pct }}%)</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400 mt-4">Sem ganhos registrados este mês.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Charts --}}
                        <div class="w-40 flex items-center justify-center">
                            <div x-show="tab === 'expense'">
                                <x-charts.category-donut
                                    id="chartGastos"
                                    :labels="$expenseLabels"
                                    :data="$expensePercentages"
                                    :colors="$expenseColors"
                                    :amounts="$expenseAmounts"
                                />
                            </div>
                            <div x-show="tab === 'income'">
                                <x-charts.category-donut
                                    id="chartGanhos"
                                    :labels="$incomeLabels"
                                    :data="$incomePercentages"
                                    :colors="$incomeColors"
                                    :amounts="$incomeAmounts"
                                />
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card class="w-full">
                    @php
                        $barMonths  = $monthly_performance->pluck('month')
                            ->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M/y'))
                            ->toArray();
                        $barIncome  = $monthly_performance->pluck('total_income')->map(fn($v) => round($v, 2))->toArray();
                        $barExpense = $monthly_performance->pluck('total_expense')->map(fn($v) => round($v, 2))->toArray();
                    @endphp

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Evolução mensal</h2>
                            <p class="text-sm text-gray-400">Últimos 6 meses</p>
                        </div>
                    </div>

                    @if($monthly_performance->isEmpty())
                        <div class="flex flex-col items-center justify-center min-h-[200px] gap-2 text-center">
                            <i class="bx bx-bar-chart-alt-2 text-4xl text-gray-200"></i>
                            <p class="text-sm text-gray-400">Sem transações nos últimos 6 meses.</p>
                        </div>
                    @else
                        <x-charts.monthly-bars
                            id="chartEvolucao"
                            :months="$barMonths"
                            :income="$barIncome"
                            :expense="$barExpense"
                        />
                    @endif
                </x-card>
            </div>


        </div>

        <!-- Coluna Direita (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Desempenho Mensal -->
            <x-card>
                @php
                    $totalIncome  = (float) ($month_performace->total_income  ?? 0);
                    $totalExpense = (float) ($month_performace->total_expense ?? 0);
                    $resultado    = $totalIncome - $totalExpense;
                    $isPositive   = $resultado >= 0;
                @endphp

                <div class="flex h-full">
                    <!-- Lado esquerdo: textos e legendas -->
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">
                                Resultado ({{ now()->translatedFormat('M/y') }})
                            </p>
                            <h3 class="text-2xl font-bold {{ $isPositive ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $isPositive ? '+' : '' }}R$ @moneyBr($resultado)
                            </h3>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Entradas</span>
                                <span class="text-sm font-bold text-emerald-600">
                                    R$ @moneyBr($totalIncome)
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Saídas</span>
                                <span class="text-sm font-bold text-rose-600">
                                    R$ @moneyBr($totalExpense)
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <x-charts.mini-bar :entradas="$totalIncome" :saidas="$totalExpense" />
                    </div>
                </div>
            </x-card>

            <!-- Contas Bancárias (Baseado na Image 1) -->
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Contas Bancárias</h2>
                        <p class="text-base font-medium text-gray-500 mt-1">
                            R$ @moneyBr($total_balance)
                        </p>
                    </div>
                    <x-link href="{{ route('accounts.index') }}">
                        Ver mais
                    </x-link>
                </div>

                <div class="space-y-4">
                    @forelse($accounts->take(3) as $account)
                        <x-bank-account-item :account="$account"></x-bank-account-item>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhuma conta cadastrada.</p>
                    @endforelse
                </div>
            </x-card>



            <!-- Dica/Insight -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-1">Insight do mês</h3>
{{--                        <p class="text-sm leading-relaxed">--}}
{{--                            Você gastou 15% menos com Alimentação comparado ao mês passado. Continue assim!--}}
{{--                        </p>--}}
                        <p class="text-sm leading-relaxed">
                            @if($expenses_by_category->isNotEmpty())
                                Sua maior despesa este mês foi com
                                <strong>{{ $expenses_by_category->first()->name }}</strong>
                                (R$ @moneyBr($expenses_by_category->first()->total) ).
                            @else
                                Nenhuma despesa registrada este mês. Ótimo começo!
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
