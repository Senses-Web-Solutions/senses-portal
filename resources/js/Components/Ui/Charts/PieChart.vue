<template>
    <div>
        <Pie
            :chart-options="chartOptions"
            :chart-data="chartData"
            :width="width"
            :height="height"
        />
    </div>
</template>
<script>
import { Pie } from 'vue-chartjs';
import currency from '../../../Filters/Currency';

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
} from 'chart.js';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement
);

export default {
    components: { Pie },
    props: {
        data: {
            type: Array,
            required: true,
        },
        width: {
            type: Number,
            default: null,
        },
        height: {
            type: Number,
            default: null,
        },
        legend: {
            type: Boolean,
            default: false,
        },
        tooltip: {
            type: Boolean,
            default: false,
        },
        currencyLabel: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        let vm = this;
        return {
            chartOptions: {
                plugins: {
                    legend: {
                        display: this.legend,
                        position: 'bottom',
                    },
                    hover: { mode: null },
                    tooltip: {
                        position: 'nearest',
                        enabled: this.tooltip,
                        caretSize: 0,
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (vm.currencyLabel) {
                                    return label + vm.currency(context.parsed);
                                } else {
                                    return (label += context.parsed);
                                }
                            },
                        },
                    },
                },
            },
        };
    },
    computed: {
        colours() {
            return this.data.map((item) => {
                return item.colour ?? '#F2F2F3';
            });
        },
        values() {
            return this.data.map((item) => {
                return item.value;
            });
        },
        labels() {
            return this.data.map((item, index) => {
                return item.label ?? 'Data ' + index;
            });
        },
        chartData() {
            return {
                labels: this.labels,
                datasets: [
                    {
                        label: 'Data',
                        backgroundColor: this.colours,
                        hoverBackgroundColor: this.colours,
                        data: this.values,
                    },
                ],
            };
        },
    },
    methods: {
        currency,
    },
};
</script>
