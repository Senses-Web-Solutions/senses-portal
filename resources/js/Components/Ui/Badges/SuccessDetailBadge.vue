<template>
    <DetailBadge
        :class="['border-success-300', clickable ? 'hover:bg-success-50 active:bg-success-100' : '']"
        :clickable="clickable"
        dot-class="bg-success-500"
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
