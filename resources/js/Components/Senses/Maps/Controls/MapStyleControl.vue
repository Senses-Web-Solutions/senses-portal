<template>
    <div>
        <label v-if="label" class="text-base block font-medium text-gray-500 capitalize ml-0.5 mb-1">Map Style</label>
        <div>
            <Tooltip class="inline-flex">
                <SecondaryButton
                    rounded="left"
                    class="translate-x-[2px]"
                    :class="{ '!bg-gray-200': light }"
                    @click="lightMap"
                >
                    <SunIcon class="h-5 w-5 text-gray-500"></SunIcon>
                </SecondaryButton>
                <template #content>Light Map</template>
            </Tooltip>
            <Tooltip class="inline-flex">
                <SecondaryButton
                    rounded="right"
                    class="translate-x-[1px]"
                    :class="{ '!bg-gray-200': !light }"
                    @click="darkMap"
                >
                <MoonIcon class="h-5 w-5 text-gray-500"></MoonIcon>
                </SecondaryButton>
                <template #content>Dark Map</template>
            </Tooltip>
        </div>
    </div>
</template>
<script>
import { SunIcon, MoonIcon } from '@heroicons/vue/outline';

import SecondaryButton from '../../../Ui/Buttons/SecondaryButton.vue';
import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        SunIcon,
        MoonIcon,
        SecondaryButton,
        Tooltip,
    },
    props: {
        label: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            light: window.localStorage.getItem('light-map') === "true",
        };
    },
    methods: {
        lightMap() {
            this.light = true;
            window.localStorage.setItem('light-map', 'true');
            EventHub.emit('light-map');
        },

        darkMap() {
            this.light = false;
            window.localStorage.setItem('light-map', '');
            EventHub.emit('light-map');
        },
    },
};
</script>
