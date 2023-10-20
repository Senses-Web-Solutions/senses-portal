<template>
    <a href="#" :selected="selected" v-if="$slots.default" :class="classes" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium">
        <slot></slot>
    </a>
</template>

<script>
export default {
    props: {
        selected: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        classes() {
            if (this.selected && this.disabled) {
                return 'border-primary-500 text-primary-600 pointer-events-none opacity-50';
            }

            if (this.selected) {
                return 'border-primary-500 text-primary-600';
            }

            if (this.disabled) {
                return 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 pointer-events-none opacity-50';
            }

            return 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300';
        }
    }
};
</script>
