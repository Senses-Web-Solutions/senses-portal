<template>
    <div>
        <Doughnut
            v-if="chartData"
            :chart-options="chartOptions"
            :chart-data="chartData"
            :width="width"
            :height="height"
        />
    </div>
</template>
<script>
import { Doughnut } from 'vue-chartjs';
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
    components: { Doughnut },
    props: {
        centerText: {
            type: String,
            default: null,
        },
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
    mounted() {
        if (this.centerText) {
            this.registerCenterText();
        }
    },
    data() {
        let vm = this;
        return {
            index: Math.random(0, 10000),
            chartOptions: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false,
                        position: 'bottom',
                    },
                    hover: { mode: null },
                    tooltip: {
                        enabled: this.tooltip,
                        callbacks: {
                            label: function (context) {
                                console.log(context);
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
                elements: {
                    center: {
                        text: this.centerText ?? '',
                        emptyText: '',
                    },
                },
            },
        };
    },
    methods: {
        registerCenterText() {
            ChartJS.register({
                id: 'textCenter' + this.index,
                beforeDraw: (chart, args, opts) => {
                    //https://stackoverflow.com/questions/20966817/how-to-add-text-inside-the-doughnut-chart-using-chart-js
                    if (
                        chart.config.type == 'doughnut' &&
                        chart.config.options.elements.center
                    ) {
                        var width = chart.width,
                            height = chart.height,
                            ctx = chart.ctx;
                        ctx.restore();
                        var fontSize = (height / 75).toFixed(2);
                        ctx.font = '300 ' + fontSize + 'em Inter, ui-sans-serif';
                        ctx.textBaseline = 'middle';

                        var text = chart.config.options.elements.center.text,
                            textX = Math.round(
                                (width - ctx.measureText(text).width) / 2
                            ),
                            textY = height / 2;
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                },
            });
        },
        currency,
    },
    unmounted() {
        ChartJS.unregister({
            id: 'textCenter' + this.index,
            beforeDraw: (chart, args, opts) => {},
        });
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
};
</script>
