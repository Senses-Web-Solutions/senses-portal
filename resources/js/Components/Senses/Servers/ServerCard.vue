<template>
    <div class="flex">
        <div class="ml-6 mt-6 relative items-center space-x-3 rounded-lg border border-zinc-200 bg-zinc-50 px-6 py-4 shadow-sm focus-within:ring-2 hover:border-zinc-500 w-64 text-center">
            <div class="flex flex-1 flex-col px-1">
                <div class="items-center w-full text-zinc-600">
                    <span class="text-md text-zinc-700 inline-flex items-center gap-x-1.5 py-0 align-middle font-normal">
                        <!-- Online Indicator -->
                        <svg class="fill-green-500 h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                            <circle cx="3" cy="3" r="3"></circle>
                        </svg>

                        <div>
                            {{ data.title }}
                        </div>
                    </span>

                    <SmallText>{{ data.ip_address }}</SmallText>
                </div>

                <div class="py-5">
                    <div class="h-40 text-center mx-auto relative">
                        <!-- Rings -->
                        <div class="circle absolute left-0 right-0" :id="'load-1-' + this.data.id" />
                        <div class="circle absolute left-0 right-0 top-[10px]" :id="'load-5-' + this.data.id" />
                        <div class="circle absolute left-0 right-0 top-[20px]" :id="'load-15-' + this.data.id" />

                        <!-- Middle Bit -->
                        <div @click="icon += 1; icon %= 4" class="absolute left-[70px] right-[70px] top-[40px]">
                            <svg v-if="icon == 3" xmlns="http://www.w3.org/2000/svg" class="text-red-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>

                            <svg v-if="icon == 2" xmlns="http://www.w3.org/2000/svg" class="text-violet-400 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                                <path d="M21 3v5h-5" />
                                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                                <path d="M8 16H3v5" />
                            </svg>

                            <svg v-if="icon == 0 || icon == 1" class="transition duration-300" :class="icon == 1 ? 'text-red-400 rotate-180' : 'text-green-400'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75"></path>
                            </svg>
                            <!-- <svg v-if="icon == 3" class="transition duration-300" :class="previousMetric && metric.load_15 > previousMetric.load_15 ? 'text-red-400 rotate-180' : 'text-green-400'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75"></path>
                            </svg> -->
                        </div>
                    </div>

                    <div v-if="metric" class="items-left text-left mt-1 space-y-4">
                        <div>
                            <div class="text-sm text-zinc-500">Disk: {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb </div>
                            <div class="w-100 rounded-full h-1.5 mt-1 fill-red-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.disk_used / (metric.disk_total))">
                                <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                    <rect :width="(metric.disk_used * 100) / (metric.disk_total) + '%'" height="100%" rx="3"></rect>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Volume 1: {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb </div>
                            <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.disk_used / (metric.disk_total))">
                                <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                    <rect :width="(metric.disk_used * 100) / (metric.disk_total) + '%'" height="100%" rx="3"></rect>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">RAM: {{ Math.round(metric.ram_used / 10000) / 100 }}Gb / {{ Math.round((metric.ram_total) / 10000) / 100 }}Gb </div>

                            <div class="w-100 rounded-full h-1.5 mt-1 fill-yellow-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.ram_used / (metric.ram_total))">
                                <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                    <rect :width="(metric.ram_used * 100) / (metric.ram_total) + '%'" height="100%" rx="3"></rect>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Swap: {{ Math.round(metric.swap_used / 10000) / 100 }}Gb / {{ Math.round((metric.swap_total) / 10000) / 100 }}Gb </div>

                            <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.swap_used / (metric.swap_total))">
                                <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                    <rect :width="(metric.swap_used * 100) / (metric.swap_total) + '%'" height="100%" rx="3"></rect>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
// Imports
import axios from 'axios';
import SmallText from '../../Ui/Text/SmallText.vue';

import useEcho from '../../../../Support/useEcho';

const echo = useEcho();

export default {
    components: {
        SmallText,
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
        echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({serverMetric}) => {
            this.previousMetric = this.metric;
            this.metric = serverMetric;

            this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
            this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
            this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
        })

        echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricUpdated', ({serverMetric}) => {
            this.previousMetric = this.metric;
            this.metric = serverMetric;

            this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
            this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
            this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
        })

        this.load();
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
