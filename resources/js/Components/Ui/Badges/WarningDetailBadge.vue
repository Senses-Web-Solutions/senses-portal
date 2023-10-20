<template>
    <DetailBadge
        :class="['border-warning-300', clickable ? 'hover:bg-warning-50 active:bg-warning-100' : '']"
        :clickable="clickable"
        dot-class="bg-warning-500"
    >
        <template #title v-if="$slots.title">
            <slot name="title"></slot>
        </template>
        <slot></slot>
    </DetailBadge>
</template>
<script>
import DetailBadge from "./DetailBadge.vue";

export default {
    components: {
        DetailBadge,
    },
    props: {
        clickable: Boolean,
    },
};
</script>
