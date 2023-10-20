<template>
    <section v-if="slotEmpty($slots.default)">
        <div class="rounded-lg border border-zinc-100 bg-purple-50 bg-opacity-25 px-8 py-5 shadow">
            <slot></slot>
        </div>
    </section>
</template>

<script>
import slotEmpty from '../../../Support/slotEmpty';

export default {
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        slotEmpty,
    },
};
</script>
