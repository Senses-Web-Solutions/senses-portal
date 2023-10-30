<template>
    <tr v-if="this.metric" class="border-b border-zinc-200 hover:bg-zinc-100/20" onclick="window.location.href = '/projects-show';">
        <td class="py-3 pl-6">
            <div class="flex items-center gap-x-2">
                <svg
                    class="h-5 w-5 fill-yellow-400 stroke-yellow-400"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"
                    ></path>
                </svg>
                <div class="flex-auto">
                    <div
                        class="text-md font-medium leading-6 text-zinc-700"
                    >
                        {{ data.title }}
                    </div>
                </div>
            </div>
        </td>

        <td class="py-3 pr-6">
            <div class="text-zinc-500">
                <span :style="'color: ' + getColour(this.metric?.load_1)">{{ this.metric?.load_1 }}</span>
                ·
                <span :style="'color: ' + getColour(this.metric?.load_5)">{{ this.metric?.load_5 }}</span>
                ·
                <span :style="'color: ' + getColour(this.metric?.load_15)">{{ this.metric?.load_15 }}</span>
            </div>
        </td>

        <td class="py-3 pr-6">
            <svg
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                class="chart"
                height="20"
                width="100%"
                aria-labelledby="title"
                role="img"
            >
                <template v-for="(metric, index) in this.metrics">
                    <g v-if="index > (this.metrics.length - 30)" class="bar" :transform="'translate(' + index * 4 + ',0)'">
                        <rect
                            :height="metric.cpu_use / 100 * 20"
                            :y="20 - (metric.cpu_use / 100 * 20)"
                            width="3"
                            class="fill-yellow-400"
                        ></rect>
                    </g>
                </template>
            </svg>
        </td>

        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_free + metric.disk_used) / 10000) / 100 }}Gb
            </div>
            <div
                class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.disk_used / (metric.disk_used + metric.disk_free))"
            >
                <svg
                    width="100%"
                    viewBox="0 0 100 5"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        :width="metric.disk_used / (metric.disk_used + metric.disk_free) * 100"
                        height="100%"
                        rx="3"
                    ></rect>
                </svg>
            </div>
        </td>
        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_free + metric.disk_used) / 10000) / 100 }}Gb
            </div>
            <div
                class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.disk_used / (metric.disk_used + metric.disk_free))"
            >
                <svg
                    width="100%"
                    viewBox="0 0 100 5"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        :width="metric.disk_used / (metric.disk_used + metric.disk_free) * 100"
                        height="100%"
                        rx="3"
                    ></rect>
                </svg>
            </div>
        </td>
        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.ram_used / 10000) / 100 }}Gb / {{ Math.round((metric.ram_free + metric.ram_used) / 10000) / 100 }}Gb
            </div>
            <div
                class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.ram_used / (metric.ram_used + metric.ram_free))"
            >
                <svg
                    width="100%"
                    viewBox="0 0 100 5"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        :width="metric.ram_used / (metric.ram_used + metric.ram_free) * 100"
                        height="100%"
                        rx="3"
                    ></rect>
                </svg>
            </div>
        </td>

        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.swap_used / 10000) / 100 }}Gb / {{ Math.round((metric.swap_free + metric.swap_used) / 10000) / 100 }}Gb
            </div>
            <div
                class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.swap_used / (metric.swap_used + metric.swap_free))"
            >
                <svg
                    width="100%"
                    viewBox="0 0 100 5"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        :width="metric.swap_used / (metric.swap_used + metric.swap_free) * 100"
                        height="100%"
                        rx="3"
                    ></rect>
                </svg>
            </div>
        </td>

        <td class="py-3 text-zinc-500">
            <span
                class="text-md text-green-500 inline-flex items-center gap-x-1.5 py-0 align-middle font-normal"
            >
                <svg
                    class="fill-green-500 h-1.5 w-1.5"
                    viewBox="0 0 6 6"
                    aria-hidden="true"
                >
                    <circle cx="3" cy="3" r="3"></circle>
                </svg>
                1 min
            </span>
        </td>
    </tr>
</template>

<script>
// Imports
import axios from 'axios';

export default {
    components: {

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
        this.circle1 = window.Circles.create({
            id: 'circles-load1-' + this.client,
            radius: 70,
            value: 0,
            maxValue: 1,
            width: 8,
            text: null,
            colors: ['#27272a', '#27272a'],
            duration: 100,
            wrpClass: 'circles-wrp',
            textClass: 'circles-text',
            valueStrokeClass: '',
            maxValueStrokeClass: 'fill-zinc-900',
            styleWrapper: true,
            styleText: true
        });

        this.circle5 = window.Circles.create({
            id: 'circles-load5-' + this.client,
            radius: 60,
            value: 0,
            maxValue: 1,
            width: 8,
            text: null,
            colors: ['#27272a', '#27272a'],
            duration: 100,
            wrpClass: 'circles-wrp',
            textClass: 'circles-text',
            valueStrokeClass: '',
            maxValueStrokeClass: 'fill-zinc-900',
            styleWrapper: true,
            styleText: true
        });

        this.circle15 = window.Circles.create({
            id: 'circles-load15-' + this.client,
            radius: 50,
            value: 0,
            maxValue: 1,
            width: 8,
            text: null,
            colors: ['#27272a', '#27272a'],
            duration: 100,
            wrpClass: 'circles-wrp',
            textClass: 'circles-text',
            valueStrokeClass: '',
            maxValueStrokeClass: 'fill-zinc-900',
            styleWrapper: true,
            styleText: true
        });

        this.load();

        setInterval(() => {
            this.load();
        }, 30000);
    },

    data() {
        return {
            metric: null,
            metrics: null,

            circle1: null,
            circle5: null,
            circle15: null,
        };
    },

    methods: {
        load() {
            axios.get('/api/v2/server-metrics?format=all').then((response) => {
                this.metric = response.data[0]
                this.metrics = response.data;

                this.circle1.update(this.metric.load_1);
                this.circle5.update(this.metric.load_5);
                this.circle15.update(this.metric.load_15);

                this.circle1.updateColors(['#27272a', this.getColour(this.metric.load_1)]);
                this.circle5.updateColors(['#27272a', this.getColour(this.metric.load_5)]);
                this.circle15.updateColors(['#27272a', this.getColour(this.metric.load_15)]);
            });
        },

        getColour(load) {
            if (load <= 0.33) {
                return '#4ade80';
            }

            if (load <= 0.66) {
                return '#fbbf24';
            }

            if (load <= 1.00) {
                return '#f87171';
            }

            if (load > 1.00) {
                return '#ae81ff';
            }
        },

        getRandom(min, max) {
          return Math.random() * (max - min) + min;
        }
    },
};
</script>
