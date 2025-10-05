import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart; // Make it globally available

window.Alpine = Alpine;

Alpine.start();
