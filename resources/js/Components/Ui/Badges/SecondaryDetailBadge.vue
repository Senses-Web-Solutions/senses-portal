<template>
    <DetailBadge
        :class="['border-secondary-300', clickable ? 'hover:bg-secondary-50 active:bg-secondary-100' : '']"
        :clickable="clickable"
        dot-class="bg-secondary-500"
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
