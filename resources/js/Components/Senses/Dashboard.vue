<template>
<div class="w-full flex flex-wrap">
    <div class="ml-6 mt-6 relative items-center space-x-3 rounded-lg border border-zinc-200 bg-zinc-50 px-6 py-4 shadow-sm focus-within:ring-2 hover:border-zinc-500 w-64 text-center">
        <div class="flex flex-1 flex-col px-1">
            <div class="items-center w-full text-zinc-600">
                <span class="text-md text-zinc-700 inline-flex items-center gap-x-1.5 py-0 align-middle font-normal">
                    <svg class="fill-green-500 h-1.5 w-1.5" viewBox="0 0 6 6" aria-hidden="true">
                        <circle cx="3" cy="3" r="3"></circle>
                    </svg>
                    IETG
                </span>
            </div>

            <div class="py-5">
                <div class="h-40 text-center mx-auto relative">
                    <div class="circle absolute left-0 right-0" :id="'circles-load1'">
                        <div class="circles-wrp text-zinc-200 dark:text-zinc-100" style="position: relative; display: inline-block;"><svg xmlns="http://www.w3.org/2000/svg" width="140" height="140">
                                <path fill="transparent" stroke="currentColor" stroke-width="8" d="M 69.98655756855612 4.0000013689315495 A 66 66 0 1 1 69.90832732408728 4.000063665784566 Z"></path>
                                <path fill="transparent" stroke="#f87171" stroke-width="8" d="M 69.98655756855612 4.0000013689315495 A 66 66 0 1 1 20.12413963534663 26.27643167846428 " class="circles-valueStroke"></path>
                            </svg>
                            <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 49px; height: 140px; line-height: 140px;"></div>
                        </div>
                    </div>
                    <div class="circle absolute left-0 right-0 top-[10px]" :id="'circles-load5'">
                        <div class="circles-wrp text-zinc-200 dark:text-zinc-100" style="position: relative; display: inline-block;"><svg xmlns="http://www.w3.org/2000/svg" width="120" height="120">
                                <path fill="transparent" stroke="currentColor" stroke-width="8" d="M 59.98859430059307 4.00000116151768 A 56 56 0 1 1 59.92221712346799 4.0000540194535645 Z"></path>
                                <path fill="transparent" stroke="#4ade80" stroke-width="8" d="M 59.98859430059307 4.00000116151768 A 56 56 0 0 1 98.28619947769383 19.132324147876503 " class="circles-valueStroke"></path>
                            </svg>
                            <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 42px; height: 120px; line-height: 120px;"></div>
                        </div>
                    </div>
                    <div class="circle absolute left-0 right-0 top-[20px]" :id="'circles-load15'">
                        <div class="circles-wrp text-zinc-200 dark:text-zinc-100" style="position: relative; display: inline-block;"><svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
                                <path fill="transparent" stroke="currentColor" stroke-width="8" d="M 49.99063103263002 4.000000954103811 A 46 46 0 1 1 49.93610692284871 4.00004437312257 Z"></path>
                                <path fill="transparent" stroke="#fbbf24" stroke-width="8" d="M 49.99063103263002 4.000000954103811 A 46 46 0 1 1 11.188199306076577 74.69097257896425 " class="circles-valueStroke"></path>
                            </svg>
                            <div class="circles-text" style="position: absolute; top: 0px; left: 0px; text-align: center; width: 100%; font-size: 35px; height: 100px; line-height: 100px;"></div>
                        </div>
                    </div>
                    <div class="circle absolute left-[70px] right-[70px] top-[40px]" id="">
                        <svg class="text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75"></path>
                        </svg>
                    </div>
                </div>
                <div v-if="metric" class="items-left text-left mt-1 space-y-4">
                    <div>
                        <div class="text-sm text-zinc-500">Space: {{ Math.round(metric.disk_free / 1000000, 2) }}Gb / {{ Math.round((metric.disk_free + metric.disk_used) / 1000000, 2) }}Gb </div>
                        <div class="w-100 rounded-full h-1.5 mt-1 fill-red-400 bg-zinc-200">
                            <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                <rect width="85%" height="100%" rx="3"></rect>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-500">Volume: {{ Math.round(metric.disk_free / 1000000, 2) }}Gb / {{ Math.round((metric.disk_free + metric.disk_used) / 1000000, 2) }}Gb </div>
                        <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-200">
                            <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                <rect width="33%" height="100%" rx="3"></rect>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-500">RAM: {{ Math.round(metric.ram_free / 1000000, 2) }}Gb / {{ Math.round((metric.ram_free + metric.ram_used) / 1000000, 2) }}Gb </div>

                        <div class="w-100 rounded-full h-1.5 mt-1 fill-yellow-400 bg-zinc-200">
                            <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                <rect width="66%" height="100%" rx="3"></rect>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-500">Swap: {{ Math.round(metric.swap_free / 1000000, 2) }}Gb / {{ Math.round((metric.swap_free + metric.swap_used) / 1000000, 2) }}Gb </div>

                        <div class="w-100 rounded-full h-1.5 mt-1 fill-green-400 bg-zinc-200">
                            <svg width="100%" viewBox="0 0 400 13" xmlns="http://www.w3.org/2000/svg">
                                <rect width="24%" height="100%" rx="3"></rect>
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

export default {
    components: {

    },

    props: {

    },

    mounted() {
        this.load();

        setInterval(() => {
            this.load();
        }, 5000);
    },

    data() {
        return {
            metric: null
        };
    },

    methods: {
        load() {
            axios.get('/api/v2/server-metrics?format=select-search').then((response) => {
                this.metric = response.data[0]
            });

            window.Circles.create({
                id: 'circles-load1',
                radius: 70,
                value: this.metric?.load_1 ?? 0,
                maxValue: 1,
                width: 8,
                text: null,
                colors: ['#27272a', '#fbbf24'],
                duration: 100,
                wrpClass: 'circles-wrp',
                textClass: 'circles-text',
                valueStrokeClass: '',
                maxValueStrokeClass: 'fill-zinc-900',
                styleWrapper: true,
                styleText: true
            });

            window.Circles.create({
                id: 'circles-load5',
                radius: 60,
                value: this.metric?.load_5 ?? 0,
                maxValue: 1,
                width: 8,
                text: null,
                colors: ['#27272a', '#fbbf24'],
                duration: 100,
                wrpClass: 'circles-wrp',
                textClass: 'circles-text',
                valueStrokeClass: '',
                maxValueStrokeClass: 'fill-zinc-900',
                styleWrapper: true,
                styleText: true
            });

            window.Circles.create({
                id: 'circles-load15',
                radius: 50,
                value: this.metric?.load_15 ?? 0,
                maxValue: 1,
                width: 8,
                text: null,
                colors: ['#27272a', '#fbbf24'],
                duration: 100,
                wrpClass: 'circles-wrp',
                textClass: 'circles-text',
                valueStrokeClass: '',
                maxValueStrokeClass: 'fill-zinc-900',
                styleWrapper: true,
                styleText: true
            });
        },

        getColour(load) {
            if (load <= 33) {
                return '#f87171';
            }

            if (load <= 66) {
                return '#fbbf24';
            }

            if (load <= 100) {
                return '#4ade80';
            }

            if (load > 100) {
                return '#ae81ff';
            }
        }
    },
};
</script>
