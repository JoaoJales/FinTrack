@props([
    'id' => 'minibar-' . uniqid(),
    'entradas' => 0,
    'saidas' => 0
])

<div
    x-data
    x-init="
        new ApexCharts($el.querySelector('[data-chart]'), {
            series: [{
                name: 'Valor (R$)',
                data: [{{ $entradas }}, {{ $saidas }}]
            }],
            chart: {
                type: 'bar',
                height: 150,
                width: 150,
                sparkline: { enabled: true }
            },
            colors: ['#10b981', '#f43f5e'],
            plotOptions: {
                bar: {
                    columnWidth: '80%',
                    borderRadius: 4,
                    distributed: true
                }
            },
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: val => 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                }
            }
        }).render()
    "
>
    <div data-chart class=""></div>
</div>
