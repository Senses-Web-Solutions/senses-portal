<template>
    <div class="absolute inset-0 z-50 flex pointer-events-none dusk-aside" v-bind="$attrs">
        <transition
            enter-active-class="transition duration-200 ease-in-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in-out delay-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="asideIndex + 1 < $asides.all.length" class="absolute inset-0 z-20 bg-black bg-opacity-50 pointer-events-auto nightwind-prevent" @click="$asides.pop()"></div>
        </transition>
        <div class="relative flex justify-end flex-grow h-full">
            <slot name="side"/>
        </div>
        <div class="relative" id="aside_layout">
            <div class="absolute top-0 left-0 z-10 flex pt-4 pr-4 -translate-x-full pointer-events-auto">
                <button aria-label="Close panel" class="p-3 text-zinc-700 transition duration-150 ease-in-out bg-white rounded-full shadow-md hover:bg-secondary-100 active:bg-secondary-300 hover:text-zinc-900" @click="$asides.pop()">
                    <XIcon class="w-6 h-6"></XIcon>
                </button>
            </div>
            <div :class="sizeClass" class="flex flex-col flex-grow w-screen h-screen max-h-screen overflow-y-auto bg-white shadow-xl pointer-events-auto relative">
                <CollapseTransition>
                    <IndeterminateLoadingBar v-if="loading" class="absolute top-0 inset-x-0"></IndeterminateLoadingBar>
                </CollapseTransition>
                <AsideHeader v-if="hasHeader" :aside-index="asideIndex">
                    <template v-if="$slots.title" #title>
                        <slot name="title"></slot>
                    </template>
                    <template v-if="$slots.actions" #actions>
                        <slot name="actions"></slot>
                    </template>
                    <template v-if="$slots['header-content']" #header-content>
                        <slot name="header-content"></slot>
                    </template>
                </AsideHeader>
                <div
                    id="aside_layer"
                    class="overflow-y-auto"
                    :class="
                        { 'grid grid-cols-3 h-full': slotEmpty($slots['right-side']), 'grid grid-cols-4 h-full': slotEmpty($slots['left-side']), 'flex-1': !slotEmpty($slots['right-side']), 'flex-1': !$slots['left-side'], 'p-4 sm:p-6': !flush && slotEmpty($slots['right-side']), 'p-4 sm:p-6': !flush && slotEmpty($slots['left-side']) }
                    "
                >
                    <div v-if="slotEmpty($slots['left-side'])" class="col-span-1 border-r">
                        <slot name="left-side"></slot>
                    </div>
                    <div :class="{'col-span-2': slotEmpty($slots['right-side']), 'col-span-3': slotEmpty($slots['left-side']), 'p-4 sm:p-6' : slotEmpty($slots['left-side']) || slotEmpty($slots['right-side']) || !flush}">
                        <slot></slot>
                    </div>
                    <div v-if="slotEmpty($slots['right-side'])" class="col-span-1 border-l" :class="{'p-4 sm:p-6': !flush}">
                        <slot name="right-side"></slot>
                    </div>
                </div>
                <div id="aside-footer">
                    <div :class="{'p-3 sm:p-6 border-t border-zinc-200' : $slots.footer}">
                        <slot name="footer"></slot>
                    </div>
                    <div :id="'aside-layout-footer-' + asideIndex"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { XIcon } from '@heroicons/vue/outline';
import IndeterminateLoadingBar from '../Ui/IndeterminateLoadingBar.vue';
import CollapseTransition from '../Ui/Transitions/CollapseTransition.vue';
import AsideHeader from './AsideHeader.vue';
import slotEmpty from "../../Support/slotEmpty";

export default {
    components: { XIcon, IndeterminateLoadingBar, CollapseTransition, AsideHeader },
    props: {
        size: {
            type: String,
            default: 'base',
            validator(value) {
                return ['xs', 'sm', 'base', 'lg', 'xl', 'base-with-side'].includes(value);
            },
        },
        asideIndex: {
            type: Number,
            required: true,
        },
        flush: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        slotEmpty
    },
    computed: {
        actionSlotID() {
            return `aside${this.asideIndex}-actions`;
        },
        sizeClass() {
            const sizeClasses = {
                xs: 'max-w-lg',
                sm: 'max-w-2xl',
                base: 'max-w-4xl',
                lg: 'max-w-6xl',
                xl: 'max-w-7xl',
                'base-with-side': 'max-w-5xl',
            };
            return sizeClasses[this.size];
        },
        hasHeader() {
            return this.$slots.title || this.$slots.actions || this.$slots['header-content'];
        }
    },
};
</script>
