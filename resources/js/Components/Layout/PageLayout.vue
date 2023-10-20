<template>
    <div
        class="overflow-y-auto shadow-inner"
        style="min-height: calc(100vh - 132px);"
        :class="{
            'p-8': !flush
        }"
    >
        <slot></slot>
    </div>
</template>
<script>
export default {
    inject: ['flushLayout'],

    props: {
        flush: {
            type: Boolean,
            default: false
        }
    },

    created() {
        this.flushLayout = this.flush;
    }
}
</script>
