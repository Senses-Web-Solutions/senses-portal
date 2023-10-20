<template>
    <DetailBadge
        :class="['border-info-300', clickable ? 'hover:bg-info-50 active:bg-info-100' : '']"
        :clickable="clickable"
        dot-class="bg-info-500"
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
