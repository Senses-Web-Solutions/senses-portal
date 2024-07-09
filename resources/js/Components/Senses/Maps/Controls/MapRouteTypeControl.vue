<template>
    <div class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px]">
        <Tooltip>
            <button 
                class="rounded-l bg-white p-2 text-gray-700 transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" 
                :class="{ 
                    '!bg-purple-300': antsOn && isDark,
                    '!bg-purple-700': antsOn && !isDark,
                }" 
                @click="antRoutes"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 20"
                    stroke-width="1.5"
                    class="h-5 w-5 stroke-black dark:stroke-white"
                    :class="{ 
                        '!stroke-white': !antsOn && isDark,
                        '!stroke-white': antsOn && isDark,
                        '!stroke-black': !antsOn && !isDark,
                        '!stroke-white': antsOn && !isDark,
                    }"
                >
                    <line x1="0" y1="20" x2="20" y2="0" stroke-dasharray="4,6" />
                </svg>
            </button>
            <template #content>Ant Routes</template>
        </Tooltip>
        <Tooltip>
            <button 
            class="-ml-[1px] rounded-r bg-white p-2 text-gray-700 transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" 
            :class="{ 
                '!bg-purple-300': !antsOn && isDark,
                '!bg-purple-700': !antsOn && !isDark,
            }" 
            @click="zipRoutes">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 20"
                    stroke-width="1.5"
                    class="h-5 w-5 stroke-black dark:stroke-white"
                    :class="{ 
                        '!stroke-white': antsOn && isDark,
                        '!stroke-white': !antsOn && isDark,
                        '!stroke-black': antsOn && !isDark,
                        '!stroke-white': !antsOn && !isDark,
                    }"
                >
                    <line x1="0" y1="20" x2="20" y2="0" />
                </svg>
            </button>
            <template #content>Line Routes</template>
        </Tooltip>
    </div>
</template>
<script>
import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        Tooltip,
    },
    props: {
        ants: {
            type: Boolean,
            required: true,
        },
    },
    emits: ['updateSetting'],
    data() {
        return {
            antsOn: this.ants,
        };
    },
    computed: {
        isDark() {
            return document.documentElement.classList.contains('dark');
        },
    },
    watch: {
        ants(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.antsOn = newVal;
                this.emitUpdate();
            }
        },
    },
    methods: {
        antRoutes() {
            this.antsOn = true;
            this.emitUpdate();
        },

        zipRoutes() {
            this.antsOn = false;
            this.emitUpdate();
        },

        emitUpdate() {
            this.$emit('updateSetting', { key: 'ants', value: this.antsOn });
            EventHub.emit('map:ants', this.antsOn);
        }
    },
};
</script>
