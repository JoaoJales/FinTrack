@props([
    'id'     => 'chart-' . uniqid(),
    'months' => [],
    'income' => [],
    'expense'=> [],
])

<div
    {{ $attributes->merge(['class' => '']) }}
    x-data
    x-init="
        new ApexCharts($el.querySelector('[data-chart]'), {
            chart: {
                type: 'bar',
                height: 200,
                toolbar: { show: false },
                animations: { enabled: true },
                fontFamily: 'inherit',
            },
            series: [
                { name: 'Ganhos',  data: {{ json_encode($income) }} },
                { name: 'Gastos',  data: {{ json_encode($expense) }} },
            ],
            xaxis: {
                categories: {{ json_encode($months) }},
                labels: {
                    style: { fontSize: '11px', colors: '#9ca3af' }
                },
                axisBorder: { show: false },
                axisTicks:  { show: false },
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', colors: '#9ca3af' },
                    formatter: val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                }
            },
            colors: ['#22c55e', '#f43f5e'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '55%',
                }
            },
            dataLabels: { enabled: false },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                markers: { width: 8, height: 8, radius: 8 },
                labels: { colors: '#6b7280' },
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: val => 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                }
            },
        }).render()
    "
>
    <div data-chart></div>
</div>
