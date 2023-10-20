<template>
    <div id="collapse" class="block select-none items-center transition duration-100 group" :class="{ 'first:rounded-t-lg last:rounded-b-lg': rounded }">
        <slot name="header">
            <div
                id="collapse-header"
                class="cursor-pointer hover:bg-opacity-100"
                :class="{
                    'group-first:rounded-t-lg': rounded,
                    'bg-purple-50 bg-opacity-50': !alternate && isOpen && parent,
                }"
                @click="toggle">
                <!-- colour change requested by matt on 7th feb - http://pm.senses.co.uk/my-work?modal=Task-19901-234 -->
                <div :class="headerClasses">
                    <div class="flex items-center justify-between">
                        <div class="justify-between" :class="{ 'truncate': !alternate, 'pr-4 flex-wrap max-w-[70%]': alternate }">
                            <p v-if="$slots.title" class="truncate transition text-zinc-700" :class="{ 'text-zinc-900': isOpen, 'text-lg': alternate }">
                                <slot name="title"></slot>
                            </p>
                            <div v-if="$slots.description" class="text-sm text-zinc-500 text-sm flex grow text-grey-500 justify-between">
                                <slot name="description"></slot>
                            </div>
                        </div>
                        <div class="w-full" v-if="alternate">
                            <div class="w-full h-0.5 my-auto bg-zinc-200 rounded" />
                        </div>
                        <div v-if="$slots.actions" class="text-sm text-zinc-500 text-sm flex text-grey-500 justify-between ml-auto">
                            <slot name="actions"></slot>
                        </div>
                        <div class="ml-2 flex flex-shrink-0 items-center space-x-2" v-if="showChevron">
                            <div class="flex items-center space-x-2">
                                <slot name="badge"></slot>
                            </div>
                            <ChevronRightIcon :class="chevronClasses" class="h-5 w-5 text-zinc-400 transition" aria-hidden="true" />
                        </div>
                    </div>
                </div>
            </div>
        </slot>
        <CollapseTransition>
            <div v-if="isOpen" class="select-text" :class="{
                'p-4': !flush,
                'border-t': divider && !alternate,
                'border-b border-b-purple-200': bottomBorder && isOpen
            }">
                <slot />
            </div>
        </CollapseTransition>
    </div>
</template>

<script>
import {
    ChevronRightIcon
} from '@heroicons/vue/solid';
import CollapseTransition from '../Transitions/CollapseTransition.vue';

export default {
    components: {
        CollapseTransition,
        ChevronRightIcon,
    },

    props: {
        alternate: {
            type: Boolean,
            default: false,
        },
        showChevron: {
            type: Boolean,
            default: true,
        },
        open: {
            type: Boolean,
            default: false,
        },
        parent: {
            type: Boolean,
            default: true,
        },
        border: {
            type: Boolean,
            default: false,
        },
        bottomBorder: {
            type: Boolean,
            default: false,
        },
        index: {
            required: false,
            type: Number,
            default: null,
        },
        flush: {
            type: Boolean,
            default: false,
        },
        flushHeader: {
            type: Boolean,
            default: false,
        },
        compact: {
            type: Boolean,
            default: false,
        },
        rounded: {
            type: Boolean,
            default: true,
        },
        // Visual doesn't have hover effects in places, we should remove this prop.
        highlight: {
            type: Boolean,
            default: false,
        },
        divider: {
            type: Boolean,
            default: true,
        },
        headerClass: {
            type: String,
            default: null,
        },
        chevronColour: {
            type: String,
            default: null,
        },
        onClick: {
            type: Function,
            default: null,
        },
    },

    data() {
        const isOpen = this.open;
        return {
            isOpen,
        };
    },

    watch: {
        open(v) {
            this.isOpen = v;
        },
    },

    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.onClick) {
                this.onClick();
            }
        },
    },

    computed: {
        headerClasses() {
            var classes = '';

            if (this.border) {
                if (this.isOpen) {
                    classes += "border border-primary-300 shadow-sm rounded-lg ";
                } else {
                    classes += "border border-zinc-300 shadow-sm rounded-lg ";
                }
            }

            if (this.headerClass) {
                return classes + this.headerClass;
            } else if (this.alternate) {
                return 'py-4';
            } else {
                return classes + 'p-4';
            }
        },
        chevronClasses() {
            return [
                this.chevronColour ? this.chevronColour : 'text-zinc-500',
                {
                    'rotate-90': this.isOpen,
                },
            ];
        },
    },
};
</script>
