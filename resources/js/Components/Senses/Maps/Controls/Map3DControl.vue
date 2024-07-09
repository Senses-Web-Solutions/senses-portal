<template>
    <div class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px]">
        <Tooltip>
            <button 
                class="rounded-l bg-white p-2 text-black transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" 
                :class="{ 
                    '!bg-purple-700 !text-white': threeDimensionalOn && !isDark,
                    '!bg-purple-300': threeDimensionalOn && isDark,
                }" 
                @click="threeDimensionalMap"
            >
                <CubeIcon class="h-5 w-5"></CubeIcon>
            </button>
            <template #content>3D Buildings</template>
        </Tooltip>
        <Tooltip>
            <button 
                class="-ml-[1px] rounded-r bg-white p-2 text-black transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" 
                :class="{ 
                    '!bg-purple-700': !threeDimensionalOn && !isDark,
                    '!bg-purple-300': !threeDimensionalOn && isDark,
                }" 
                @click="twoDimensionalMap"
            >
                <div 
                    class="h-5 w-5 border-2 border-black rounded scale-[72%]" 
                    :class="{ 
                        '!border-white': !threeDimensionalOn && !isDark,
                        '!border-black': !threeDimensionalOn && isDark,
                    }"
                    >
                </div>
            </button>
            <template #content>2D Buildings</template>
        </Tooltip>
    </div>
</template>
<script>
import { CubeIcon } from '@heroicons/vue/outline';

import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        CubeIcon,
        Tooltip,
    },
    props: {
        threeDimensional: {
            type: Boolean,
            required: true,
        }
    },
    emits: ['updateSetting'],
    data() {
        return {
            threeDimensionalOn: this.threeDimensional,
        };
    },
    computed: {
        isDark() {
            return document.documentElement.classList.contains('dark');
        },
    },
    watch: {
        threeDimensional(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.threeDimensionalOn = newVal;
            }
        },
    },
    methods: {
        threeDimensionalMap() {
            this.threeDimensionalOn = true;
            this.emitUpdate();
        },

        twoDimensionalMap() {
            this.threeDimensionalOn = false;
            this.emitUpdate();
        },

        emitUpdate() {
            this.$emit('updateSetting', { key: 'three_dimensional', value: this.threeDimensionalOn });
            EventHub.emit('map:three_dimensional', this.threeDimensionalOn);
        },
    },
};
</script>
