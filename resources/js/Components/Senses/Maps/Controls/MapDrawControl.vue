<template>
    <div class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px]">
        <Tooltip>
            <button
                v-if="polygon"
                class="rounded-l bg-white p-2 text-black transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300"
                :class="{ 
                    '!bg-purple-700 !text-white': polygonMode && !isDark,
                    '!bg-purple-300': polygonMode && isDark
                }"
                @click="emitPolygon"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-spline"
                >
                    <circle
                        cx="19"
                        cy="5"
                        r="2"
                    />
                    <circle
                        cx="5"
                        cy="19"
                        r="2"
                    />
                    <path d="M5 17A12 12 0 0 1 17 5" />
                </svg>
            </button>
            <template #content>Draw Polygon</template>
        </Tooltip>
        <Tooltip>
            <button
                v-if="trash"
                class="-ml-1 rounded-r bg-white p-2 text-black transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300"
                @click="emitTrash"
            >
                <TrashIcon class="h-5 w-5" />
            </button>
            <template #content>Remove Last Drawing</template>
        </Tooltip>
    </div>
</template>
<script>
import { TrashIcon } from '@heroicons/vue/outline';
import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        TrashIcon,
        Tooltip,
    },
    props: {
        polygon: {
            type: Boolean,
            default: true,
        },
        trash: {
            type: Boolean,
            default: true,
        },
    },
    emits: ['polygon', 'trash'],
    data() {
        return {
            polygonMode: false,
            trashMode: false,
        };
    },
    computed: {
        isDark() {
            return document.documentElement.classList.contains('dark');
        },
    },
    mounted() {
        EventHub.on('map:draw_mode', this.polygonToggle);

        EventHub.on('map:draw_off', this.polygonOff);
    },

    beforeUnmount() {
        EventHub.off('map:draw_mode', this.polygonToggle);
        EventHub.off('map:draw_off', this.polygonOff);
    },

    methods: {
        emitPolygon() {
            this.polygonMode = !this.polygonMode;
            this.$emit('polygon');
        },

        emitTrash() {
            this.polygonMode = false;
            this.$emit('trash');
        },

        polygonToggle(on) {
            this.polygonMode = !on;
        },

        polygonOff() {
            this.polygonMode = false;
        },
    },

};
</script>
