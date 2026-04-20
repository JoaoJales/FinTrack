@props([
    'id' => 'chart-' . uniqid(),
    'labels' => [],
    'data' => [],
    'colors' => ['#fbbf24', '#22d3ee', '#6366f1', '#ea580c', '#3b82f6', '#e5e7eb']
])

<div
    {{ $attributes->merge(['class' => '']) }}
    x-data
    x-init="
        new ApexCharts($el.querySelector('[data-chart]'), {
            chart: {
                type: 'donut',
                height: 200,
                animations: { enabled: true }
            },
            series: {{ json_encode($data) }},
            labels: {{ json_encode($labels) }},
            colors: {{ json_encode($colors) }},
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: false },
                            value: {
                                show: true,
                                fontSize: '14px',
                                fontWeight: 'bold',
                                color: '#6b7280',
                                formatter: val => val + '%'
                            },
                            total: {
                                show: true,
                                fontSize: '10px',
                                color: '#9ca3af',
                                formatter: () => 'Março'
                            }
                        }
                    }
                }
            },
            tooltip: { enabled: true }
        }).render()
    "
>
    <div data-chart></div>
</div>
