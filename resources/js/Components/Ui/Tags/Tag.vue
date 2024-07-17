<template>
    <!-- <a v-if="$slots.default" :href="href" class="text-sm-old relative inline-flex max-w-full items-center rounded-full border bg-white px-3 py-0.5" :class="[highlightBorder ? `border-${colour}` : 'border-zinc-300', `bg-gradient-to-b from-${colour}-50 to-white`]"> -->
        <a v-if="$slots.default" :href="href" class="text-sm-old relative inline-flex max-w-full items-center rounded-full border bg-white px-3 py-0.5" :class="[highlightBorder ? `border-${colour}` : 'border-zinc-300']">
        <span v-if="colour && highlightDot" class="absolute flex shrink-0 items-center justify-center">
            <span class="h-1.5 w-1.5 rounded-full" :class="`bg-${colour}`" aria-hidden="true"></span>
        </span>
        <span :class="[colour && highlightDot ? 'ml-3.5' : null, 'text-' + size ]" class="truncate text-zinc-700">
            <slot></slot>
        </span>
        <span v-if="$slots.end">
            <slot name="end"></slot>
        </span>
        <span v-if="hasArrow">
            <ChevronDownIcon class="w-3 h-3 ml-2"></ChevronDownIcon>
        </span>
    </a>
</template>

<script>
import {
    ChevronDownIcon
} from '@heroicons/vue/outline';

export default {
    components: {
        ChevronDownIcon,
    },
    props: {
        colour: {
            type: String,
            default: null,
        },
        highlightBorder: {
            type: Boolean,
            default: false,
        },
        highlightDot: {
            type: Boolean,
            default: true,
        },
        hasArrow: {
            type: Boolean,
            default: false,
        },
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom' , 'xs', 'sm', 'md', 'lg'].includes(
                    value
                );
            },
        },
        href: {
            type: String,
            default: '#',
        }
    },
    computed: {
        // textClasses() {
        //     const classes = {
        //         xs: 'text-sm px-2.5 py-0.5',
        //         sm: 'px-3 py-0.5',
        //     };

        //     let returnClasses = classes[this.size];
        //     return returnClasses;
        // },
    }
};
</script>
