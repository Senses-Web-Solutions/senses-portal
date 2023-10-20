<template>
    <header v-if="hasNormalHeader || slotEmpty($slots['header-content'])" class="border-b border-zinc-200 divide-y divide-zinc-200 dark:divide-zinc-800" :class="{'mt-6': hasNormalHeader}">
        <div class="flex items-center justify-between px-4 pb-6 sm:px-6" v-if="hasNormalHeader">
            <div class="flex items-center space-x-4">
                <h2 class="text-3xl font-medium leading-7">
                    <slot name="title"></slot>
                </h2>
            </div>
            <div :id="`aside${asideIndex}-actions`">
                <slot name="actions"></slot>
            </div>
        </div>
        <div v-if="slotEmpty($slots['header-content'])" class="px-4 py-6 sm:px-6">
            <slot name="header-content"></slot>
        </div>
    </header>
</template>
<script>
import { Comment, Text } from 'vue';
import slotEmpty from "../../Support/slotEmpty";
export default {
    props: {
        asideIndex: {
            type: Number,
            required: true
        }
    },
    computed:{
        hasNormalHeader() {
            return this.slotEmpty(this.$slots.title) || this.slotEmpty(this.$slots.actions);
        }
    },
    methods:{
        //Really dumb as slots return a comment when null
        slotEmpty
    }
}
</script>
