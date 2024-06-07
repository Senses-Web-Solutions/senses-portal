<template>
    <div :title="hrDuration(timeSinceLastUpdate)" class="rounded-lg border px-6 py-4 shadow-sm cursor-pointer w-64 text-center flex flex-col"
        :class="true ? 'border-zinc-200 bg-zinc-50 hover:border-zinc-300 hover:bg-zinc-100' : 'border-red-200 bg-red-50 hover:border-red-300 hover:bg-red-100'" @click="goToServer">
        <div class="w-full">
            <span class="flex items-center justify-center text-xl font-medium text-zinc-700">
                <!-- Verified Indicator -->
                <svg :class="'mr-2 h-1.5 w-1.5 ' + (this.data.verified_at && true ? 'fill-green-500' : 'fill-red-500')" viewBox="0 0 6 6" aria-hidden="true">
                    <circle cx="3" cy="3" r="3"></circle>
                </svg>

                {{ data.title }}
            </span>

            <SmallText>{{ this.data.verified_at ? data.ip_address : 'Un-Verified' }}</SmallText>
        </div>

        <div class="py-5">
            <div class="h-40 text-center mx-auto relative">
                <!-- Rings -->
                <div class="circle absolute left-0 right-0" :id="'load-1-' + this.data.id" />
                <div class="circle absolute left-0 right-0 top-[10px]" :id="'load-5-' + this.data.id" />
                <div class="circle absolute left-0 right-0 top-[20px]" :id="'load-15-' + this.data.id" />

                <!-- Middle Bit -->
                <div class="absolute left-[70px] right-[70px] top-[37px]">
                    <svg v-if="icon == 'danger' && !dangerIgnored" @click.stop="dangerIgnored = true; updateStatus();" xmlns="http://www.w3.org/2000/svg" class="text-red-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>

                    <svg v-if="icon == 'deploy_success'" xmlns="http://www.w3.org/2000/svg" class="text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <svg v-if="icon == 'idle'" xmlns="http://www.w3.org/2000/svg" class="text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                    </svg>

                    <svg v-if="icon == 'unverified'" xmlns="http://www.w3.org/2000/svg" class="text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m19 5 3-3" />
                        <path d="m2 22 3-3" />
                        <path d="M6.3 20.3a2.4 2.4 0 0 0 3.4 0L12 18l-6-6-2.3 2.3a2.4 2.4 0 0 0 0 3.4Z" />
                        <path d="M7.5 13.5 10 11" />
                        <path d="M10.5 16.5 13 14" />
                        <path d="m12 6 6 6 2.3-2.3a2.4 2.4 0 0 0 0-3.4l-2.6-2.6a2.4 2.4 0 0 0-3.4 0Z" />
                    </svg>

                    <svg v-if="icon == 'deploying'" xmlns="http://www.w3.org/2000/svg" class="text-violet-500 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                        <path d="M21 3v5h-5" />
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                        <path d="M8 16H3v5" />
                    </svg>

                    <svg v-if="icon == 'load_up' || icon == 'load_down'" class="transition duration-300" :class="icon == 'load_up' ? 'text-red-400 rotate-180' : 'text-green-400'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75"></path>
                    </svg>

                    <div v-if="icon == 'show_load'" @click.stop="showLoadAsPercentage = !showLoadAsPercentage" class="font-bold text-2xl mt-1 text-red-400" :style="`color: ${getColour(metric.load_1 / data.cpu_cores)}`">
                        <div class="mt-5" v-if="showLoadAsPercentage">
                            {{ Math.round(metric.load_1 / data.cpu_cores * 100) }}%
                        </div>
                        <div v-else class="mt-2">
                            {{ metric.load_1 }} <br><div class="border-b mx-2 h-0 border-red-400" :style="`border-color: ${getColour(metric.load_1 / data.cpu_cores)}`"></div> {{ data.cpu_cores }}
                        </div>
                    </div>

                    <!-- <svg v-if="icon == 3" class="transition duration-300" :class="previousMetric && metric.load_15 > previousMetric.load_15 ? 'text-red-400 rotate-180' : 'text-green-400'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75"></path>
                        </svg> -->
                </div>
            </div>

            <div class="items-left text-left mt-1 space-y-4">
                <div>
                    <div class="text-sm text-zinc-500">CPU: {{ metric.cpu_use }}% / 100% </div>

                    <div class="w-100 rounded-full h-1.5 mt-1 fill-yellow-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.cpu_use / 100)">
                        <svg v-if="this.metric.cpu_use" width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                            <rect :width="metric.cpu_use + '%'" height="100%" rx="3"></rect>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-zinc-500">Disk: {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb </div>
                    <div class="w-100 rounded-full h-1.5 mt-1 fill-red-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.disk_used / (metric.disk_total ?? 1))">
                        <svg v-if="this.metric.disk_used" width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                            <rect :width="(metric.disk_used * 100) / (metric.disk_total ?? 1) + '%'" height="100%" rx="3"></rect>
                        </svg>
                    </div>
                </div>
                <!-- <div>
                    <div class="text-sm text-zinc-500">Volume 1: {{ Math.round(metric.disk_used / 10000) / 100 }}Gb / {{ Math.round((metric.disk_total) / 10000) / 100 }}Gb </div>
                    <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.disk_used / (metric.disk_total ?? 1))">
                        <svg v-if="this.metric.disk_used" width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                            <rect :width="(metric.disk_used * 100) / (metric.disk_total ?? 1) + '%'" height="100%" rx="3"></rect>
                        </svg>
                    </div>
                </div> -->
                <div>
                    <div class="text-sm text-zinc-500">RAM: {{ Math.round(metric.ram_used / 10000) / 100 }}Gb / {{ Math.round((metric.ram_total) / 10000) / 100 }}Gb </div>

                    <div class="w-100 rounded-full h-1.5 mt-1 fill-yellow-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.ram_used / (metric.ram_total ?? 1))">
                        <svg v-if="this.metric.ram_used" width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                            <rect :width="(metric.ram_used * 100) / (metric.ram_total ?? 1) + '%'" height="100%" rx="3"></rect>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Swap: {{ Math.round(metric.swap_used / 10000) / 100 }}Gb / {{ Math.round((metric.swap_total) / 10000) / 100 }}Gb </div>

                    <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-300" :style="'fill: ' + this.getColour(metric.swap_used / (metric.swap_total ?? 1))">
                        <svg v-if="this.metric.swap_used" width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                            <rect :width="(metric.swap_used * 100) / (metric.swap_total ?? 1) + '%'" height="100%" rx="3"></rect>
                        </svg>
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

import useEcho from '../../../Support/useEcho';

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
        this.circle1 = this.createCircle('load-1-' + this.data.id, 70, this.data.cpu_cores ?? 1);
        this.circle5 = this.createCircle('load-5-' + this.data.id, 60, this.data.cpu_cores ?? 1);
        this.circle15 = this.createCircle('load-15-' + this.data.id, 50, this.data.cpu_cores ?? 1);

        if (this.data.verified_at) {
            echo.private(`servers.${this.data.id}.server-metrics`).listen('ServerMetrics\\ServerMetricCreated', ({serverMetric}) => {
                console.log(serverMetric);

                this.lastRecievedTimestamp = new Date().getTime() / 1000;

                this.previousMetric = this.metric;
                this.metric = serverMetric;

                this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
                this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
                this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));

                this.updateStatus();
            })

            echo.private(`servers.${this.data.id}.deploy`).listen('Servers\\ServerDeployed', ({data}) => {
                console.log(data);

                if (data.status == 'running') {
                    this.updateStatus('deploying');
                } else if (data.status == 'completed') {
                    this.updateStatus('deploy_success');

                    setTimeout(() => {
                        this.updateStatus();
                    }, 10000);
                } else {
                    this.updateStatus('danger');
                }
            })

            setInterval(() => {
                this.timeSinceLastUpdate = Math.round(new Date().getTime() / 1000 - (this.lastRecievedTimestamp ?? this.metric.timestamp));
            }, 1000);

            this.load();
        } else {
            this.updateStatus();
        }
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

                cpu_use: 0,
            },

            previousMetric: null,

            circle1: null,
            circle5: null,
            circle15: null,

            icon: 0,

            dangerIgnored: false,
            showLoadAsPercentage: true,

            timeSinceLastUpdate: 0,
        };
    },

    methods: {
        updateStatus(status=null) {
            if (!this.data.verified_at) {
                this.icon = "unverified";
                console.log("Setting status to " + "unverified")
                return;
            }

            if (status) {
                this.icon = status;
                console.log("Setting status to " + status)
                return;
            }

            if (this.icon == 'deploying') {
                console.log("Blocked status change as server is currently deploying");
                return;
            }

            if (!this.dangerIgnored && (this.metric.disk_used / this.metric.disk_total >= 0.9)) {
                this.icon = 'danger';
                console.log("Setting status to " + 'danger')
            } else if (!this.dangerIgnored && (this.metric.ram_used / this.metric.ram_total >= 0.9)) {
                this.icon = 'danger';
                console.log("Setting status to " + 'danger')
            // } else if (!this.dangerIgnored && (this.metric.swap_used / this.metric.swap_total >= 0.9)) {
            //     this.icon = 'danger';
            //     console.log("Setting status to " + 'danger')
            // } else if (this.metric.load_1 >= this.data.cpu_cores) {
            } else {
                this.icon = 'show_load';
                console.log("Setting status to " + 'show_load')
            // } else if (this.metric.load_15 <= this.previousMetric.load_15) {
            //     this.icon = 'load_down';
            //     console.log("Setting status to " + 'load_down')
            // } else if (this.metric.load_15 > this.previousMetric.load_15) {
            //     this.icon = 'load_up';
            //     console.log("Setting status to " + 'load_up')
            // } else {
                // this.icon = 'idle';
                // console.log("Setting status to " + 'idle')
            }
        },

        updateCircle(circle, value, colour) {
            circle.update(value);
            circle.updateColors(['currentColor', colour]);
        },

        goToServer() {
            window.location.href = '/servers/' + this.data.id;
        },

        load() {
            axios.get('/api/v2/servers/' + this.data.id + '/server-metrics?format=limited&limit=50').then((response) => {
                if (response.data.length > 0) {
                    this.metric = response.data[0];
                    this.previousMetric = response.data[1] ?? null;

                    if (this.data.cpu_cores) {
                        this.updateCircle(this.circle1, this.metric.load_1, this.getColour(this.metric.load_1 / this.data.cpu_cores));
                        this.updateCircle(this.circle5, this.metric.load_5, this.getColour(this.metric.load_5 / this.data.cpu_cores));
                        this.updateCircle(this.circle15, this.metric.load_15, this.getColour(this.metric.load_15 / this.data.cpu_cores));
                    }

                    this.updateStatus();
                }
            });
        },

        getColour(load) {
            if (load < 0.50) {
                return '#4ade80';
            }

            if (load < 0.90) {
                return '#fbbf24';
            }

            if (load < 1.00) {
                return '#f87171';
            }

            if (load >= 1.00) {
                return '#ef4444';
            }

            // if (load <= 0.50) {
            //     return '#4ade80';
            // }

            // if (load <= 0.90) {
            //     return '#fbbf24';
            // }

            // if (load <= 1.00) {
            //     return '#ef4444';
            // }

            // if (load > 1.00) {
            //     return '#ae81ff';
            // }

            // if (load <= 0.50) {
            //     return '#4ade80';
            // }

            // if (load <= 0.90) {
            //     return '#fbbf24';
            // }

            // if (load <= 1.00) {
            //     return '#fb723c';
            // }

            // if (load > 1.00) {
            //     return '#ef4444';
            // }
        },

        hrDuration (seconds) {
            if (!seconds) {
                return '0 Secs';
            }

            const days = Math.floor(seconds / 86400);
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor(seconds / 60) % 60;
            const secs = seconds % 60;

            const dayString = days > 1 ? 'Days' : 'Day';
            const hourString = hours > 1 ? 'Hrs' : 'Hr';
            const minString = mins > 1 ? 'Mins' : 'Min';
            const secString = secs > 1 ? 'Secs' : 'Sec';

            let string = [];

            hours ? string.push(`${hours} ${hourString}`) : string;
            mins ? string.push(`${mins} ${minString}`) : string;
            hours ? string : string.push(`${secs} ${secString}`);

            days ? string = [`${days} ${dayString}`] : string;

            string = string.join(" ");

            return string;
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
