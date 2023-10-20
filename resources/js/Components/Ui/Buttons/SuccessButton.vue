<template>
    <SeButton :class="classes" :size="size" :rounded="rounded">
        <template v-if="$slots.icon" #icon>
            <slot name="icon" class="inline-block w-5 h-5 my-auto"></slot>
        </template>
        <slot></slot>
    </SeButton>
</template>
<script>
import SeButton from './SeButton.vue';

export default {
    components: {
        SeButton,
    },
    props: {
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['xs', 'sm', 'md', 'lg', 'xl'].includes(value);
            },
        },
        rounded: {
            type: String,
            default: 'full',
        },
        // outline: {
        //     type: Boolean,
        //     default: false
        // }
    },
    computed: {
        classes() {
            return [
                'border-transparent',
                'text-white',
                'bg-success-600',
                'hover:bg-success-500',
                'focus:outline-none',
                'focus:border-success-700',
                'focus:ring-success-400',
                'active:bg-success-700',
            ];
        },
    },
};
</script>
