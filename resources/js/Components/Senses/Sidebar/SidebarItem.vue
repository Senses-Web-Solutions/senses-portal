<template>
    <a :href="to" class="text-md group flex cursor-pointer items-center rounded-md px-2 py-1.5" v-bind="$attrs" :class="{
            'bg-primary-700 text-white': active,
            'text-zinc-500 hover:bg-primary-200': !active,
        }">

        <div class="w-4 h-4 mr-2 shrink-0">
            <slot name="icon">
                <StarIcon />
            </slot>
        </div>

        <Tooltip class="flex items-center flex-grow truncate mr-3 h-4">
            <div v-if="title" class="truncate" :class="{ 'text-white': active, 'text-zinc-800': !active }">
                {{ title }}
            </div>
            <IndeterminateLoadingBar v-else></IndeterminateLoadingBar>
            <template #content>
                {{ title == '' ? 'Loading...' : title }}
            </template>
        </Tooltip>
        <ChevronRightIcon v-if="arrow" class="h-3 w-3 transition shrink-0" :class="{ 'rotate-90': open }"></ChevronRightIcon>
    </a>
    <slot></slot>
</template>

<script>
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import Tooltip from '../../Ui/Tooltip.vue';
import {
    StarIcon,
    ChevronRightIcon
} from '@heroicons/vue/outline';

export default {
    components: {
        StarIcon,
        ChevronRightIcon,
        Tooltip,
        IndeterminateLoadingBar,
    },
    inject: ['sidebar'],
    props: {
        to: {
            type: String,
            default: '#',
        },
        title: {
            type: String,
            required: true,
        },
        active: {
            type: Boolean,
            default: false,
        },
        open: {
            type: Boolean,
            default: false,
        },
        arrow: {
            type: Boolean,
            default: false,
        },
        isInGroup: {
            type: Boolean,
            default: false
        }
    },
    data() {
        const {
            title,
            to
        } = this;
        const {
            icon
        } = this.$slots;
        return {
            item: {
                title,
                to,
                icon
            },
        };
    },
    mounted() {
        if (!this.isInGroup) {
            this.sidebar.push(this.item);
        }
    },
};
</script>
