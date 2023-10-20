<template>
    <div class="p-5" :class="{'rounded-md': !flush, 'hidden': hide }">
        <div class="flex">
            <div class="shrink-0">
                <div :class="iconClass"
                     class="w-5 h-5 transition duration-150 ease-in-out">
                    <slot name="icon"></slot>
                </div>
            </div>
            <div class="ml-3 space-y-2 w-full">
                <h3 :class="titleClass" v-if="slotEmpty($slots.title)" class="font-medium">
                    <slot name="title"></slot>
                </h3>
                <p v-if="slotEmpty($slots.default)" :class="textClass" class="text-sm-old">
                    <slot></slot>
                </p>
            </div>
            <div class="pl-3 ml-auto" v-if="dismissable">
                <div class="-mx-1.5 -my-1.5">
                    <button @click.stop="hide = true" type="button"
                            class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-1 focus:ring-offset-2"
                            :class="textClass">
                        <span class="sr-only">Dismiss</span>
                        <XIcon class="w-5 h-5" aria-hidden="true"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {XIcon} from '@heroicons/vue/outline';
import slotEmpty from "../../../Support/slotEmpty";

export default {
    components: {XIcon},
    props: {
        iconClass: String,
        titleClass: String,
        textClass: String,
        flush: Boolean,
        dismissable: Boolean
    },
    data() {
        return {
            hide: false
        }
    },
    methods: {
        slotEmpty
    }
}
</script>
