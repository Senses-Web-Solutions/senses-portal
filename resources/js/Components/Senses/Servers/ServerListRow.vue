<template>
    <tr class="border-b border-zinc-200 hover:bg-zinc-200 cursor-pointer" @click="goToServer">
        <td class="py-3 pl-6">
            <div class="flex items-center gap-x-2">
                <svg class="h-5 w-5 fill-yellow-400 stroke-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"></path>
                </svg>
                <div class="flex-auto">
                    <div class="text-md font-medium leading-6 text-zinc-700 cursor-pointer">
                        {{ data.title }}
                    </div>
                </div>
            </div>
        </td>

        <td class="py-3 pr-6">
            <div class="text-zinc-500 text-center">
                <span :style="'color: ' + getColour(this.metric?.load_1 / (this.data.cpu_cores ?? 1))">{{ this.metric?.load_1 }}</span>
                ·
                <span :style="'color: ' + getColour(this.metric?.load_5 / (this.data.cpu_cores ?? 1))">{{ this.metric?.load_5 }}</span>
                ·
                <span :style="'color: ' + getColour(this.metric?.load_15 / (this.data.cpu_cores ?? 1))">{{ this.metric?.load_15 }}</span>
            </div>
        </td>

        <td class="py-3 pr-6">
            <svg v-if="this.metrics.length > 0 && this.data.cpu_cores" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="chart" height="20" width="100%" aria-labelledby="title" role="img">
                <template v-for="(serverMetric, index) in this.metrics">
                    <g v-if="index < 30" class="bar" :transform="'translate(' + (116 - index * 4) + ',0)'">
                        <rect width="3" :height="serverMetric.load_1 / this.data.cpu_cores * 20 + 1" :y="19 - (serverMetric.load_1 / this.data.cpu_cores * 20)" :style="'fill: ' + getColour(serverMetric.load_1 / this.data.cpu_cores)"></rect>
                    </g>
                </template>
            </svg>
            <div v-else class="text-sm text-zinc-500">
                N/A
            </div>
        </td>

        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb
            </div>
            <div class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.disk_used / metric.disk_total)">
                <svg v-if="this.metric.disk_total > 0" width="100%" viewBox="0 0 100 5" xmlns="http://www.w3.org/2000/svg">
                    <rect :width="metric.disk_used / (metric.disk_total) * 100" height="100%" rx="3"></rect>
                </svg>
            </div>
        </td>
        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb
            </div>
            <div class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.disk_used / metric.disk_total)">
                <svg v-if="this.metric.disk_total > 0" width="100%" viewBox="0 0 100 5" xmlns="http://www.w3.org/2000/svg">
                    <rect :width="metric.disk_used / metric.disk_total * 100" height="100%" rx="3"></rect>
                </svg>
            </div>
        </td>
        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.ram_used / 10000) / 100 }}Gb / {{ Math.round(metric.ram_total / 10000) / 100 }}Gb
            </div>
            <div class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.ram_used / metric.ram_total)">
                <svg v-if="this.metric.ram_total > 0" width="100%" viewBox="0 0 100 5" xmlns="http://www.w3.org/2000/svg">
                    <rect :width="metric.ram_used / metric.ram_total * 100" height="100%" rx="3"></rect>
                </svg>
            </div>
        </td>

        <td class="py-3">
            <div class="text-sm text-zinc-500">
                {{ Math.round(metric.swap_used / 10000) / 100 }}Gb / {{ Math.round(metric.swap_total / 10000) / 100 }}Gb
            </div>
            <div class="rounded-full h-1.5 mt-1 bg-zinc-200 w-3/4" :style="'fill: ' + getColour(metric.swap_used / metric.swap_total)">
                <svg v-if="this.metric.swap_total > 0" width="100%" viewBox="0 0 100 5" xmlns="http://www.w3.org/2000/svg">
                    <rect :width="metric.swap_used / metric.swap_total * 100" height="100%" rx="3"></rect>
                </svg>
            </div>
        </td>

        <td v-if="this.data.verified_at" class="py-3 text-zinc-500">
            <span class="text-md text-green-500 inline-flex items-center gap-x-1.5 py-0 align-middle font-normal">
                <svg class="fill-green-500 h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                    <circle cx="3" cy="3" r="3"></circle>
                </svg>
                {{ timeSinceLastUpdate }}
            </span>
        </td>

        <td v-else class="py-3 text-zinc-500" title="Install script to verify server.">
            <span class="text-md text-red-500 inline-flex items-center gap-x-1.5 py-0 align-middle font-normal">
                <svg class="fill-red-500 h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                    <circle cx="3" cy="3" r="3"></circle>
                </svg>
                Un-Verified
            </span>
        </td>
    </tr>
</template>

<script>
// Imports
import axios from 'axios';
import SmallText from '../../Ui/Text/SmallText.vue';

import useEcho from '../../../../Support/useEcho';

import { formatDistanceToNow } from 'date-fns';

const echo = useEcho();

export default {
    components: {
        SmallText,
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    },

    mounted() {
        if (!this.data.verified_at) {
            return;
        }

        echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({serverMetric}) => {
            this.previousMetric = this.metric;
            this.metric = serverMetric;

            this.metrics.unshift(serverMetric);
        })

        echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricUpdated', ({serverMetric}) => {
            this.previousMetric = this.metric;
            this.metric = serverMetric;

            this.metrics.unshift(serverMetric);
        })

        setInterval(() => {
            this.timeSinceLastUpdate = this.hrDuration(Math.round(new Date().getTime() / 1000 - this.metric.timestamp));
        }, 1000);

        this.load();
    },

    data() {
        return {
            metric: {
                load_1: 0,
                load_5: 0,
                load_15: 0,

                disk_free: 0,
                disk_used: 0,
                disk_total: 0,

                ram_free: 0,
                ram_used: 0,
                ram_total: 0,

                swap_free: 0,
                swap_used: 0,
                swap_total: 0,

                timestamp: 0,
            },

            metrics: [],
            previousMetric: null,

            timeSinceLastUpdate: 1,
        };
    },

    methods: {
        formatDistanceToNow,
        hrDuration (seconds) {
            if (!seconds) {
                return 'N/A';
            }

            const days = Math.floor(seconds / 86400);
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor(seconds / 60) % 60;
            const secs = seconds % 60;

            const dayString = days > 1 ? ' Days' : ' Day';
            const hourString = hours > 1 ? ' Hrs' : ' Hr';
            const minString = mins > 1 ? ' Mins' : ' Min';
            const secString = secs > 1 ? ' Secs' : ' Sec';

            let string = [];

            hours ? string.push(`${hours} ${hourString}`) : string;
            mins ? string.push(`${mins} ${minString}`) : string;
            hours ? string : string.push(`${secs} ${secString}`);

            days ? string = [`${days} ${dayString}`] : string;

            string = string.join(" ");

            return string;
        },

        load() {
            axios.get('/api/v2/servers/' + this.data.id + '/server-metrics?format=limited&limit=50').then((response) => {
                if (response.data.length > 0) {
                    this.metric = response.data[0];
                    this.metrics = response.data;

                    this.previousMetric = response.data[1] ?? null;
                }
            });
        },

        goToServer() {
            window.location.href = '/servers/' + this.data.id;
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
    },
};
</script>
