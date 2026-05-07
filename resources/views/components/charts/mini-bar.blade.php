@props([
    'id' => 'minibar-' . uniqid(),
    'entradas' => 0,
    'saidas' => 0
])

<div class="w-full max-w-[120px] sm:max-w-[150px] shrink-0 mx-auto sm:mx-0"
    x-data
    x-init="
        new ApexCharts($el.querySelector('[data-chart]'), {
            series: [{
                name: 'Valor (R$)',
                data: [{{ $entradas }}, {{ $saidas }}]
            }],
            chart: {
                type: 'bar',
                height: 140,
                width: '100%',
                sparkline: { enabled: true }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 120 },
                },
            }],
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
    <div data-chart class="w-full min-h-[120px]"></div>
</div>
