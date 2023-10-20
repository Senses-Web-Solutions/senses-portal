<template>
    <DetailBadge
        :class="['border-danger-300', clickable ? 'hover:bg-danger-50 active:bg-danger-100' : '']"
        :clickable="clickable"
        dot-class="bg-danger-500"
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
