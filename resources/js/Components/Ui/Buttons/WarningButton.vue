<template>
    <SeButton :class="classes" :size="size" :rounded="rounded">
        <template #icon v-if="$slots.icon">
            <slot name="icon" class="h-5 w-5 my-auto"></slot>
        </template>
        <slot></slot>
    </SeButton>
</template>
<script>
import SeButton from "./SeButton.vue";

export default {
    props: {
        size: {
            type: String,
            default: "md",
            validator(value) {
                return ["xs", "sm", "md", "lg", "xl"].includes(value);
            },
        },
        rounded:{
            type:String,
            default:"full"
        },
        // outline: {
        //     type: Boolean,
        //     default: false
        // }
    },
    components: {
        SeButton,
    },
    computed: {
        classes() {
            return [
                'border-transparent',
                'text-white',
                'bg-warning-600',
                'hover:bg-warning-500',
                'focus:outline-none',
                'focus:border-warning-700',
                'focus:ring-warning-400',
                'active:bg-warning-700',
            ];
        },
    },
};
</script>
