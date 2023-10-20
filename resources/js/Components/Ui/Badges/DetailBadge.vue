<template>
    <div>
        <p v-if="$slots.title && noFloatingLabel" class="pb-2 text-zinc-500 transition" :class="labelClass">
            <slot name="title"></slot>
        </p>
        <a href="#" :class="classes" class="group relative inline-flex min-w-48 items-center rounded-md border px-3 outline-none transition" style="padding-top: 0.17rem; padding-bottom: 0.17rem;" @click="$emit('badge-click', $event)">
            <div v-if="$slots.title && !noFloatingLabel" class="absolute max-w-40 -top-2 text-sm text-zinc-500 transition px-1" :class="labelClass">
                <div :title="title" class="z-10 relative truncate">
                    <slot name="title"></slot>
                </div>
                <!-- cover the border with a 1px line, better than a white background as it allows the background hover to bleed through -->
                <div class="absolute inset-x-0 top-[7px] h-px bg-white"></div>
            </div>
            <div class="py-1.5 flex-1 truncate">
                <div class="flex items-center gap-3">
                    <span class="h-1.5 w-1.5 rounded-full transition" :class="[dotClass, 'bg-' + colour]" aria-hidden="true"></span>
                    <p class="my-auto text-zinc-900 truncate">
                        <slot></slot>
                    </p>
                    <ChevronDownIcon v-if="hasArrow && !loading" class="h-4 w-4 text-zinc-500 ml-auto" />
                    <LoadingIcon v-else-if="hasArrow" class="h-4 w-4 ml-auto"></LoadingIcon>
                </div>
            </div>
        </a>
    </div>
</template>

<script>
import {
    ChevronDownIcon
} from '@heroicons/vue/outline';
import LoadingIcon from '../LoadingIcon.vue';

export default {
    components: {
        ChevronDownIcon,
        LoadingIcon
    },
    props: {
        dotClass: {
            type: String,
        },
        hasArrow: {
            type: Boolean,
        },
        labelClass: {
            type: String,
        },
        loading: {
            type: Boolean,
        },
        colour: {
            type: String,
            default: 'gray-500',
        },
        clickable: {
            type: Boolean,
            default: false,
        },
        noFloatingLabel: {
            type: Boolean,
            default: false
        },
        fullWidth: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: '',
        },
    },
    emits: ['badge-click'],
    computed: {
        classes() {
            var classes = '';
            classes += 'border-' + this.colour;
            if (this.clickable) {
                classes +=
                    ' hover:bg-opacity-10 hover:bg-' +
                    this.colour +
                    ' cursor-pointer';
            } else {
                classes += ' cursor-default';
            }
            if (this.fullWidth) {
                classes += ' w-full';
            }
            return classes;
        },
    },
};
</script>
