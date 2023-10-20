<template>
    <div>
        <Bar
            :width="width"
            :height="height"
            :chart-options="chartOptions"
            :chart-data="chartData"
            :chart-styles="chartStyles"
        />
    </div>
</template>
<script>
import { Bar } from 'vue-chartjs';
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
    components: { Bar },
    props: {
        data: {
            type: Array,
            required: true,
        },
        labels: {
            type: Array,
            required: false,
        },
        scales: {
            type: [Boolean, String],
            default: false,
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
        percentageLabel: {
            type: Boolean,
            default: false,
        },
        width: {
            type: Number,
            default: 200,
        },
        height: {
            type: Number,
            default: 300,
        },
    },
    data() {
        let vm = this;
        return {
            chartOptions: {
                scales: {
                    x: {
                        display: this.scales == true || this.scales == 'x',
                    },
                    y: {
                        display: this.scales == true || this.scales == 'y',
                        ticks: {
                            callback: function (value, index, ticks) {
                                if (vm.currencyLabel) {
                                    return vm.currency(value);
                                } else if (vm.percentageLabel){
                                    return value + '%'
                                } else {
                                    return value;
                                }
                            },
                        },
                    },
                },
                hover: { mode: null },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: this.legend,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            boxWidth: 10,
                            boxHeight: 10,
                        },
                    },
                    tooltip: {
                        enabled: this.tooltip,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (vm.currencyLabel) {
                                    if (context.parsed.y !== null) {
                                        label += vm.currency(context.parsed.y);
                                    }
                                } else if (vm.percentageLabel){
                                    label += context.parsed.y + '%'
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            },
                        },
                    },
                },
            },
        };
    },
    methods: {
        currency,
    },
    computed: {
        chartData() {
            var datasets = this.data?.map((dataset, index) => {
                return {
                    label: dataset.label ?? 'Data ' + index,
                    data: dataset.values,
                    backgroundColor: dataset.colours,
                };
            });

            return {
                labels: this.labels
                    ? this.labels
                    : this.data
                    ? Array.from(
                          Array(Object.keys(this.data[0].values).length).keys()
                      )
                    : [],
                datasets: datasets,
            };
        },
        chartStyles() {
            return {
                width: '100%',
                height: '100%',
                position: 'relative',
            };
        },
    },
};
</script>
