<template>
    <DetailBadge
        :class="['border-primary-300', clickable ? 'hover:bg-primary-50 active:bg-primary-100' : '']"
        :clickable="clickable"
        dot-class="bg-primary-500"
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
