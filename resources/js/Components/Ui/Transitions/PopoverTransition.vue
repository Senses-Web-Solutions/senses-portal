<template>
    <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-95 opacity-0"
        @enter="$emit('enter', $event)"
    >
        <slot></slot>
    </transition>
</template>
<script setup>
defineProps({
    as: {
        type: String,
        default: 'transition'
    }
})

defineEmits(['enter']);
</script>