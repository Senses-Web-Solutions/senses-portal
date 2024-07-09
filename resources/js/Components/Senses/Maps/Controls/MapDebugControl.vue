<template>
    <div v-if="isSenses" class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px]">
        <Tooltip>
            <button class="rounded bg-white p-2 transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" :class="{ '!bg-purple-700 !text-white': debug }" @click="toggleMapDebug">
                <ChipIcon class="h-5 w-5" />
            </button>
            <template #content>{{ message }}</template>
        </Tooltip>
    </div>
</template>
<script>

import {ChipIcon} from '@heroicons/vue/outline';

import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';
import user from '../../../../Support/user';

export default {
    components: {
        Tooltip,
        ChipIcon
    },
    emits: ['updateSetting'],
    data() {
        return {
            debug: false,
        };
    },
    computed: {
        isSenses() {
            if (user()?.email === 'matt@senses.co.uk') return false;
            
            return user()?.email?.includes('senses.co.uk');
        },
        message() {
            return this.debug ? 'Stop Debugging Map' : 'Debug Map';
        },
    },
    methods: {
        toggleMapDebug() {
            this.debug = !this.debug;
            EventHub.emit('map:debug', this.debug);
        },
    },
};
</script>
