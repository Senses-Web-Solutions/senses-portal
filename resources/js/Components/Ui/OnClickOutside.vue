<template>
    <slot></slot>
</template>
<script>
import { onClickOutside } from '@vueuse/core';

export default {
    emits: ['click-outside'],
    setup(props, { slots, emit }) {
        onClickOutside(slots.default(), (event) => emit('click-outside', event));
    },
};
</script>
