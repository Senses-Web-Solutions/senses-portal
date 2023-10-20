<template>
    <section class="sticky top-0 z-40 flex w-full items-center justify-between border-b border-b-zinc-200 bg-white p-4 backdrop-blur-lg dark:border-b-zinc-800" :class="{
            'shadow-sm': !flushLayout,
            'border-b': flushLayout,
        }" data-component="PageHeader">
        <div class="my-1 space-y-2 truncate">
            <!-- This needs to have atleast leading 7 otherwise g and other hanging letters get cut off -->
            <h3 id="page-header-title" class="truncate align-middle text-3xl font-medium leading-7 text-zinc-900">
                <slot name="title"></slot>
            </h3>
            <p class="truncate text-base font-medium leading-4 text-zinc-500" v-if="slotEmpty($slots.subtitle)">
                <slot name="subtitle"></slot>
            </p>
        </div>
        <ButtonGroup>
            <slot name="actions"></slot>
        </ButtonGroup>
    </section>
</template>

<script>

import ButtonGroup from '../Ui/Buttons/ButtonGroup.vue';
import slotEmpty from "../../Support/slotEmpty";

export default {
    components: {
        ButtonGroup
    },

    inject: ['flushLayout'],

    methods: {
        slotEmpty
    }
};

</script>
