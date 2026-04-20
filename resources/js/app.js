import './bootstrap';
import ApexCharts from 'apexcharts';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask'

window.ApexCharts = ApexCharts;

Alpine.plugin(mask)

window.Alpine = Alpine;

Alpine.start();
