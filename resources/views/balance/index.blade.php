<x-app-layout>
    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detalhes do Saldo</h1>
                <p class="text-gray-500 mt-1">Evolução e resultados de {{ now()->year }}</p>
            </div>
            <a href="{{ route('dashboard.index') }}"
               class="inline-flex items-center gap-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">

                <i class="bx bx-arrow-back text-lg"></i>
                <span class="text-lg">Voltar</span>
            </a>
        </div>

        {{-- Gráfico de linha --}}
        <x-card class="mb-6">
            <x-charts.balance-evolution-chart
                :labels="$line_chart['labels']"
                :income="$line_chart['income']"
                :expense="$line_chart['expense']"
                :net="$line_chart['net']"
                :today="\Carbon\Carbon::create()->month(now()->month)->translatedFormat('M')"
            />
        </x-card>

        {{-- Resumo do ano --}}
        <x-card class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-6">
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-gray-800">Resultado do ano</h2>
                    <p class="text-sm text-gray-400">De 01/01/{{ $year_summary['year'] }} até 31/12/{{ $year_summary['year'] }}</p>
                </div>
                <div class="text-left sm:text-right shrink-0">
                    <p class="text-sm text-gray-400 mb-0.5">Saldo acumulado</p>
                    <p class="text-2xl font-bold {{ $year_summary['positive'] ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $year_summary['positive'] ? '+' : '' }}R$ @moneyBr($year_summary['net'])
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                        <i class="bx bx-trending-up text-xl text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-600 font-medium">Total de Ganhos</p>
                        <p class="text-lg font-bold text-emerald-700">R$ @moneyBr($year_summary['income'])</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-100">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                        <i class="bx bx-trending-down text-xl text-rose-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-rose-500 font-medium">Total de Gastos</p>
                        <p class="text-lg font-bold text-rose-600">R$ @moneyBr($year_summary['expense'])</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Resultado por mês --}}
        <x-card>
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Resultado por mês</h2>
                <p class="text-sm text-gray-400">Detalhamento mensal de {{ $year_summary['year'] }}</p>
            </div>

            <div class="space-y-3 mb-5">
                @forelse($monthly_results as $month)
                    @php
                        $total = $month['income'] + $month['expense'];
                        $incomeWidth = $total > 0 ? round(($month['income'] / $total) * 100) : 50;
                        $expenseWidth = 100 - $incomeWidth;
                    @endphp
                    <div
                        class="rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition-all duration-150 p-4 space-y-3"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                    {{ $month['positive'] ? 'bg-emerald-50' : 'bg-rose-50' }}">
                                    <i class="bx {{ $month['positive'] ? 'bx-trending-up text-emerald-500' : 'bx-trending-down text-rose-500' }} text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $month['month_label'] }}</p>
                                    <p class="text-xs {{ $month['positive'] ? 'text-emerald-600' : 'text-rose-500' }} font-medium">
                                        {{ $month['positive'] ? 'Resultado positivo' : 'Resultado negativo' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 sm:hidden">
                                <p class="text-xs text-gray-400">Resultado</p>
                                <p class="text-sm font-bold {{ $month['positive'] ? 'text-emerald-600' : 'text-rose-500' }} tabular-nums">
                                    {{ $month['positive'] ? '+' : '' }}R$ @moneyBr($month['net'])
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:hidden">
                            <div>
                                <p class="text-xs text-gray-400">Ganhos</p>
                                <p class="text-sm font-semibold text-emerald-600 tabular-nums">R$ @moneyBr($month['income'])</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Gastos</p>
                                <p class="text-sm font-semibold text-rose-500 tabular-nums">R$ @moneyBr($month['expense'])</p>
                            </div>
                        </div>

                        <div class="flex-1 hidden sm:flex items-center gap-2 min-w-0">
                            <div class="flex-1 h-1.5 rounded-full overflow-hidden bg-gray-100">
                                <div class="h-full flex">
                                    <div class="bg-emerald-400 h-full rounded-l-full transition-all" style="width: {{ $incomeWidth }}%"></div>
                                    <div class="bg-rose-400 h-full rounded-r-full transition-all" style="width: {{ $expenseWidth }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex flex-wrap items-center justify-end gap-6">
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Ganhos</p>
                                <p class="text-sm font-semibold text-emerald-600 tabular-nums">R$ @moneyBr($month['income'])</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Gastos</p>
                                <p class="text-sm font-semibold text-rose-500 tabular-nums">R$ @moneyBr($month['expense'])</p>
                            </div>
                            <div class="text-right min-w-[7rem]">
                                <p class="text-xs text-gray-400">Resultado</p>
                                <p class="text-sm font-bold {{ $month['positive'] ? 'text-emerald-600' : 'text-rose-500' }} tabular-nums">
                                    {{ $month['positive'] ? '+' : '' }}R$ @moneyBr($month['net'])
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 gap-3 text-center">
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl text-gray-300">
                            <i class="bx bx-bar-chart-alt-2"></i>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Nenhuma transação registrada em {{ $year_summary['year'] }}.</p>
                    </div>
                @endforelse
            </div>
        </x-card>

    </div>
</x-app-layout>
