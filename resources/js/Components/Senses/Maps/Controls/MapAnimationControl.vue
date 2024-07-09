<template>
    <div class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px]">
        <Tooltip>
            <button class="rounded bg-white p-2 text-black transition duration-150 ease-in-out hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300" @click="toggleRoutes">
                <PlayIcon v-if="!animateOn" class="h-5 w-5"></PlayIcon>
                <PauseIcon v-else class="h-5 w-5"></PauseIcon>
            </button>
            <template #content>{{ animateOn ? 'Pause Routes' : 'Play Routes' }}</template>
        </Tooltip>
    </div>
</template>
<script>
// External
import { PlayIcon, PauseIcon } from '@heroicons/vue/outline';

// Components
import Tooltip from '../../../Ui/Tooltip.vue';

// Support
import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        PlayIcon,
        PauseIcon,
        Tooltip,
    },
    props: {
        animate: {
            type: Boolean,
            required: true,
        },
    },
    emits: ['updateSetting'],
    data() {
        return {
            animateOn: this.animate,
        };
    },
    watch: {
        animate(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.animateOn = newVal;
            }
        },
    },
    methods: {
        toggleRoutes() {
            this.animateOn = !this.animateOn;
            this.$emit('updateSetting', { key: 'animate', value: this.animateOn });
            EventHub.emit('map:animate', this.animateOn);
        },
    },
};
</script>
