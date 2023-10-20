<template>
    <component
        :size="size"
        :is="aboveMargin ? 'SuccessBadge' : 'DangerBadge'"
    >
        <component
            v-if="arrow"
            :is="aboveMargin ? 'ArrowUpIcon' : 'ArrowDownIcon'"
            class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center"
            :class="aboveMargin ? 'text-green-500' : 'text-red-500'"
        />
        {{ convertedPercentage }}%
    </component>
</template>
<script>
import SuccessBadge from './SuccessBadge.vue';
import DangerBadge from './DangerBadge.vue';
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/outline';

export default {
    components: {
        SuccessBadge,
        ArrowUpIcon,
        DangerBadge,
        ArrowDownIcon,
    },
    props: {
        size: {
            type: String,
            default: 'sm',
            validator(value) {
                return ['xs', 'sm'].includes(value);
            },
        },
        percentage: {
            type: [Number, String],
            default: 0,
        },
        arrow: {
            type: Boolean,
            default: true,
        },
        margin: {
            type: [Number, String],
            default: null,
        },
    },
    computed: {
        aboveMargin() {
            if (this.margin) {
                return this.proxyPercentage > this.proxyMargin;
            }
            return this.proxyPercentage > 0;
        },
        convertedPercentage() {
            if (this.arrow && this.proxyPercentage < 0 & !this.proxyMargin) {
                return this.proxyPercentage * -1;
            }
            return this.proxyPercentage;
        },
        proxyPercentage(){
            return this.percentage ? parseFloat(this.percentage).toFixed(0) : 0;
        },
        proxyMargin(){
            return this.margin ? parseFloat(this.margin).toFixed(0) : 0;
        }
    },
};
</script>
