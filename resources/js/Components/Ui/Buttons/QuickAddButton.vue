<template>
    <SeButton :class="classes" size="custom" font-weight="font-base"  id="quick_add_button">
        <template #icon v-if="$slots.icon">
            <slot name="icon" class="h-5 w-5 my-auto"></slot>
        </template>
        <slot>Add</slot>
    </SeButton>
</template>
<script>
import SeButton from "./SeButton.vue";
export default {
    props: {
    },
    components: {
        SeButton,
    },
    computed: {
        classes() {
            return [
                'px-3',
                'py-0.5',
                'text-sm',
                'border-secondary-300',
                'text-secondary-700',
                'bg-white',
                'hover:bg-secondary-100',
                'focus:outline-none',
                'focus:border-secondary-500',
                'focus:ring-secondary-400',
                'active:bg-secondary-300',
            ];
        },
    },
};
</script>
