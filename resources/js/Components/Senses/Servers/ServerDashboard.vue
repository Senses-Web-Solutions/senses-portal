<template>
    <div class="lg:mx-0 lg:max-w-none">
        <div class="bg-zinc-50">
            <dl class="mx-auto grid max-w-full grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:px-2 xl:px-0">
                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 border-r border-zinc-100 px-8 py-8">
                    <dt class="text-md font-medium leading-6 text-zinc-600">
                        <p>Loads</p>

                        <p class="text-sm text-zinc-400">4 CPU Server</p>
                    </dt>
                    <dd>
                        <div class="inline-flex rounded-md whitespace-nowrap px-2 py-0.5 text-sm font-normal ring-1 ring-inset text-zinc-600 bg-zinc-100 ring-zinc-400/20 !text-sky-700 !bg-sky-100 !ring-sky-300/20">
                            Normal
                        </div>
                    </dd>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-zinc-400">
                        22% · 28% · 31%</dd>
                </div>
                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 border-r border-zinc-100 px-8 py-8">
                    <dt class="text-md font-medium leading-6 text-zinc-600">
                        <p>RAM</p>

                        <p class="text-sm text-zinc-400">2.2GB of 4GB</p>

                    </dt>
                    <dd>
                        <div class="inline-flex rounded-md whitespace-nowrap px-2 py-0.5 text-sm font-normal ring-1 ring-inset text-zinc-600 bg-zinc-100 ring-zinc-400/20 !text-red-700 !bg-red-100 !ring-red-300/20">
                            High
                        </div>
                    </dd>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-zinc-400">
                        52%</dd>
                </div>
                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 border-r border-zinc-100 px-8 py-8">
                    <dt class="text-md font-medium leading-6 text-zinc-600">
                        <p>SWAP</p>

                        <p class="text-sm text-zinc-400">8.0GB of 10GB</p>

                    </dt>
                    <dd>
                        <div class="inline-flex rounded-md whitespace-nowrap px-2 py-0.5 text-sm font-normal ring-1 ring-inset text-zinc-600 bg-zinc-100 ring-zinc-400/20 !text-sky-700 !bg-sky-100 !ring-sky-300/20">
                            Normal
                        </div>
                    </dd>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-zinc-400">
                        81%</dd>
                </div>
                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 border-r border-zinc-100 px-8 py-8">
                    <dt class="text-md font-medium leading-6 text-zinc-600">
                        <p>Disks</p>

                        <p class="text-sm text-zinc-400">Primary &amp; 2 Volumes</p>

                    </dt>
                    <dd>
                        <div class="inline-flex rounded-md whitespace-nowrap px-2 py-0.5 text-sm font-normal ring-1 ring-inset text-zinc-600 bg-zinc-100 ring-zinc-400/20">
                            Safe
                        </div>
                    </dd>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-zinc-400">
                        22% · 28% · 31%
                    </dd>
                </div>

            </dl>
        </div>

        <div class="w-full max-w-full px-4 pt-4">
            <div class="relative min-w-0 flex-col break-words rounded-2xl border border-zinc-400 bg-zinc-50">

                <div class="mb-0 p-6 pb-0 flex w-full ">
                    <div class="flex-1 text-2xl font-medium leading-10 tracking-tight text-zinc-400">
                        Load</div>

                    <div class="flex ">
                        <p class="text-sm leading-normal">
                        </p>
                        <div class="gap-x-1.5  items-center inline-flex whitespace-nowrap px-1 py-0.5 text-md font-normal inset  fill-pink-700 text-pink-700">
                            <svg class="h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                                <circle cx="3" cy="3" r="3"></circle>
                            </svg> 1 Minute
                        </div>
                        <div class="gap-x-1.5  items-center inline-flex whitespace-nowrap px-1 py-0.5 text-md font-normal inset  fill-purple-700 text-purple-700">
                            <svg class="h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                                <circle cx="3" cy="3" r="3"></circle>
                            </svg> 5 Minutes
                        </div>
                        <div class="gap-x-1.5  items-center inline-flex whitespace-nowrap px-1 py-0.5 text-md font-normal inset  fill-indigo-700 text-indigo-700">
                            <svg class="h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                                <circle cx="3" cy="3" r="3"></circle>
                            </svg> 15 Minutes
                        </div>
                        <p></p>
                    </div>
                </div>
                <div class="p-4">
                    <div>
                        <canvas id="chart-line-1" class="h-48 w-max" style="display: block; box-sizing: border-box; height: 192px; width: 1614px;" width="1614" height="192"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full max-w-full px-4 pt-4">
            <div class="relative min-w-0 flex-col break-words rounded-2xl border border-zinc-400 bg-zinc-50">

                <div class="mb-0 p-6 pb-0 flex w-full ">
                    <div class="flex-1 text-2xl font-medium leading-10 tracking-tight text-zinc-400">
                        RAM</div>

                    <div class="flex ">
                        <p class="text-sm leading-normal">
                        </p>
                        <div class="gap-x-1.5  items-center inline-flex whitespace-nowrap px-1 py-0.5 text-md font-normal inset  fill-pink-700 text-pink-700">
                            <svg class="h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                                <circle cx="3" cy="3" r="3"></circle>
                            </svg> Usage
                        </div>

                        <p></p>
                    </div>
                </div>
                <div class="p-4">
                    <div>
                        <canvas id="chart-line-2" class="h-48 w-max" style="display: block; box-sizing: border-box; height: 192px; width: 1614px;" width="1614" height="192"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full max-w-full px-4 pt-4">
            <div class="relative min-w-0 flex-col break-words rounded-2xl border border-zinc-400 bg-zinc-50">

                <div class="mb-0 p-6 pb-0 flex w-full ">
                    <div class="flex-1 text-2xl font-medium leading-10 tracking-tight text-zinc-400">
                        RAM</div>

                    <div class="flex ">
                        <p class="text-sm leading-normal">
                        </p>
                        <div class="gap-x-1.5  items-center inline-flex whitespace-nowrap px-1 py-0.5 text-md font-normal inset  fill-pink-700 text-pink-700">
                            <svg class="h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                                <circle cx="3" cy="3" r="3"></circle>
                            </svg> Usage
                        </div>

                        <p></p>
                    </div>
                </div>
                <div class="p-4">
                    <div>
                        <SeChart :data="data" class="h-48 w-max" style="display: block; box-sizing: border-box; height: 192px; width: 1614px;" width="1614" height="192"></SeChart>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- <div class="w-full h-[calc(100vh-128px)] flex items-center justify-center">
        <div class="bg-zinc-200 rounded-lg w-1/2 p-8 space-y-4">
            <div class="text-black">
                To intitialise your server, SSH into your server and run this command.
            </div>
            <div class="bg-zinc-300 rounded-lg w-1/2 p-8">

            </div>
        </div>
    </div> -->
</template>

<script>
// Imports
import axios from 'axios';
import SmallText from '../../Ui/Text/SmallText.vue';
import SeChart from '../UI/SeChart.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';

import useEcho from '../../../../Support/useEcho';

const echo = useEcho();

export default {
    components: {
        SmallText,
        SeChart,
        PrimaryButton,
    },

    props: {
        client: {
            type: String
        },
        data: {
            type: Object,
            required: true
        }
    },

    mounted() {
        // echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({
        //     serverMetric
        // }) => {
        //     this.previousMetric = this.metric;
        //     this.metric = serverMetric;

        //     this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
        //     this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
        //     this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
        // })

        // echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricUpdated', ({
        //     serverMetric
        // }) => {
        //     this.previousMetric = this.metric;
        //     this.metric = serverMetric;

        //     this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
        //     this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
        //     this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
        // })

        // this.load();

        this.createChart(document.getElementById('chart-line-1').getContext('2d'));
        this.createChart(document.getElementById('chart-line-2').getContext('2d'));
    },

    data() {
        return {
            metric: null,
            previousMetric: null,

            circle1: null,
            circle5: null,
            circle15: null,

            icon: 0,
        };
    },

    methods: {
        updateCircle(circle, value, colour) {
            circle.update(value);
            circle.updateColors(['currentColor', colour]);
        },

        load() {
            axios.get('/api/v2/servers/' + this.data.id + '/server-metrics?format=all').then((response) => {
                this.metric = response.data[0];
                this.previousMetric = response.data[1] ?? null;

                this.circle1 = this.createCircle('load-1-' + this.data.id, 70, this.data.cpu_cores);
                this.circle5 = this.createCircle('load-5-' + this.data.id, 60, this.data.cpu_cores);
                this.circle15 = this.createCircle('load-15-' + this.data.id, 50, this.data.cpu_cores);

                this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
                this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
                this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
            });
        },

        getColour(load) {
            if (load <= 0.50) {
                return '#4ade80';
            }

            if (load <= 0.90) {
                return '#fbbf24';
            }

            if (load <= 1.00) {
                return '#f87171';
            }

            if (load > 1.00) {
                return '#ae81ff';
            }
        },

        createCircle(id, radius, max) {
            return window.Circles.create({
                id: id,
                radius: radius,
                value: 0,
                maxValue: max,
                width: 8,
                text: null,
                colors: ['currentColor', 'currentColor'],
                duration: 100,
                wrpClass: 'text-zinc-200',
                maxValueStrokeClass: 'text-zinc-200',
            });
        },

        createChart(chart) {
            var gradientStroke1 = chart.createLinearGradient(0, 230, 0, 50);

            gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
            gradientStroke1.addColorStop(0, "rgba(0, 0, 0, 0)"); //purple colors

            var gradientStroke2 = chart.createLinearGradient(0, 230, 0, 50);

            gradientStroke2.addColorStop(1, "rgba(20,23,39,0.2)");
            gradientStroke2.addColorStop(0.2, "rgba(72,72,176,0.0)");
            gradientStroke2.addColorStop(0, "rgba(20,23,39,0)"); //purple colors


            var gradientStroke3 = chart.createLinearGradient(0, 130, 0, 20);

            gradientStroke3.addColorStop(1, "rgba(10,23,39,0.2)");
            gradientStroke3.addColorStop(0.2, "rgba(62,72,176,0.0)");
            gradientStroke3.addColorStop(0, "rgba(60,23,39,0)"); //???

            new Chart(chart, {
                type: "line",
                data: {
                    labels: ["9am", "10am", "11am", "12pm", "1pm", "2pm", "3pm", "4pm", "5pm"],
                    datasets: [{
                            label: "Load - 1 Min",
                            tension: 0.4,
                            borderWidth: 0,
                            pointRadius: 0,
                            borderColor: "#cb0c9f",
                            borderWidth: 3,
                            backgroundColor: gradientStroke1,
                            fill: true,
                            data: [650, 40, 300, 220, 500, 250, 400, 230, 500],
                            maxBarThickness: 6,
                        },
                        // {
                        //     label: "WebsLoad - 5 Mins",
                        //     tension: 0.4,
                        //     borderWidth: 0,
                        //     pointRadius: 0,
                        //     borderColor: "#3A416F",
                        //     borderWidth: 3,
                        //     backgroundColor: gradientStroke2,
                        //     fill: true,
                        //     data: [330, 90, 40, 140, 290, 290, 340, 230, 400],
                        //     maxBarThickness: 6,
                        // },
                        // {
                        //     label: "Load - 15 Mins",
                        //     tension: 0.4,
                        //     borderWidth: 0,
                        //     pointRadius: 0,
                        //     borderColor: "#3A416F",
                        //     borderWidth: 3,
                        //     backgroundColor: gradientStroke3,
                        //     fill: true,
                        //     data: [1440, 190, 20, 190, 890, 890, 840, 1230, 1400],
                        //     maxBarThickness: 6,
                        // },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    interaction: {
                        intersect: false,
                        mode: "index",
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5],
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: "#b2b9bf",
                                font: {
                                    size: 11,
                                    style: "normal",
                                    lineHeight: 2,
                                },
                            },
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5],
                            },
                            ticks: {
                                display: true,
                                color: "#b2b9bf",
                                padding: 20,
                                font: {
                                    size: 11,
                                    style: "normal",
                                    lineHeight: 2,
                                },
                            },
                        },
                    },
                },
            });
        }
    },
};
</script>

<style>
@keyframes pulse {
    50% {
        opacity: 0.1;
    }
}

.animate-pulse {
    animation: pulse 0.5s linear infinite;
}
</style>
