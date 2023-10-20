<template>
    <SeButton
        :class="classes"
        :size="size"
        :rounded="rounded">
        <template v-if="$slots.icon" #icon>
            <slot name="icon" class="h-5 w-5 my-auto"></slot>
        </template>
        <slot></slot>
    </SeButton>
</template>
<script>
import SeButton from './SeButton.vue';

export default {
    props: {
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['xs', 'sm', 'md', 'lg', 'xl'].includes(value);
            }
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
        SeButton
    },
    computed: {
        classes() {
            return [
                'border-transparent',
                'text-white',
                'bg-primary-600',
                'hover:bg-primary-500',
                'focus:outline-none',
                'focus:border-primary-700',
                'focus:ring-primary-400',
                'active:bg-primary-700',
            ];
        }
    }
}
</script>
