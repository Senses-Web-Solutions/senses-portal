<template>
    <SeButton :class="classes" :size="size" :rounded="rounded">
        <template v-if="$slots.icon" #icon>
            <slot name="icon"></slot>
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
                return ['custom', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl'].includes(
                    value
                );
            },
        },
        rounded: {
            type: String,
            default: 'full',
        }
    },
    computed: {
        classes() {
            return [
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
