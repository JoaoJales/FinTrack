@props([
    'id' => 'chart-' . uniqid(),
    'labels' => [],
    'income' => [],
    'expense' => [],
    'net' => [],
    'today' => null,
])

<div
    {{ $attributes->merge(['class' => '']) }}
    x-data="{
        view: 'all',
        chart: null,
        allSeries: [
            { name: 'Ganhos',    data: {{ json_encode($income) }} },
            { name: 'Gastos',    data: {{ json_encode($expense) }} },
            { name: 'Resultado', data: {{ json_encode($net) }} },
        ],
        netSeries: [
            { name: 'Resultado', data: {{ json_encode($net) }} },
        ],
        updateView(type) {
            this.view = type;
            this.chart.updateOptions({
                series: type === 'all' ? this.allSeries : this.netSeries
            });
        }
    }"
    x-init="
        chart = new ApexCharts($el.querySelector('[data-chart]'), {
            chart: {
                type: 'line',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                animations: { enabled: true },
            },
            series: allSeries,
            xaxis: {
                categories: {{ json_encode($labels) }},
                labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', colors: '#9ca3af' },
                    formatter: val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 0 })
                }
            },
            colors: ['#22c55e', '#f43f5e', '#6366f1'],
            stroke: { curve: 'smooth', width: 2.5 },
            markers: { size: 4, hover: { size: 6 } },
            @if($today)
            annotations: {
                xaxis: [{
                    x: '{{ $today }}',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    strokeDashArray: 4,
                    label: {
                        text: 'Hoje',
                        style: { fontSize: '10px', color: '#6366f1', background: '#eef2ff' }
                    }
                }]
            },
            @endif
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            legend: { show: false },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                }
            },
        });
        chart.render();
    "
>
    {{-- Header do Gráfico com Toggle --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Evolução mensal</h2>
            <p class="text-sm text-gray-400">Ganhos, gastos e resultado líquido</p>
        </div>

        <div class="flex bg-gray-100 rounded-lg p-1 gap-1">
            <button
                type="button"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                :class="view === 'all' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-400 hover:text-gray-600'"
                x-on:click="updateView('all')"
            >Todos</button>
            <button
                type="button"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                :class="view === 'net' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-400 hover:text-gray-600'"
                x-on:click="updateView('net')"
            >Resultado</button>
        </div>
    </div>

    {{-- Área do Gráfico --}}
    <div data-chart id="{{ $id }}"></div>
</div>
