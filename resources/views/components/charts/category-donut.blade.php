@props([
    'id' => 'chart-' . uniqid(),
    'labels' => [],
    'data' => [],
    'amounts' => [],
    'colors' => []
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
                                formatter: () => '{{ now()->translatedFormat('F') }}'
                            }
                        }
                    }
                }
            },
            tooltip: {
                enabled: true,
                custom: function({ seriesIndex, w }) {
                    const amounts = {{ json_encode($amounts) }};
                    const label   = w.globals.labels[seriesIndex];
                    const pct     = w.globals.series[seriesIndex];
                    const color   = w.globals.colors[seriesIndex];

                    const value = amounts.length > 0
                        ? 'R$ ' + Number(amounts[seriesIndex]).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                        : pct + '%';

                    return `
                        <div style='padding: 8px 12px; font-size: 12px; font-family: inherit; background: #fff;'>
                            <div style='display: flex; align-items: center; gap: 6px; margin-bottom: 2px;'>
                                <span style='display:inline-block; width:10px; height:10px; border-radius:50%; background:${color};'></span>
                                <span style='color: #374151; font-weight: 600;'>${label}</span>
                            </div>
                            <div style='color: #111827; font-weight: 700; font-size: 13px; padding-left: 16px;'>${value}</div>
                            <div style='color: #9ca3af; font-size: 11px; padding-left: 16px;'>${pct}% do total</div>
                        </div>
                    `;
                }
            }
        }).render()
    "
>
    <div data-chart></div>
</div>
