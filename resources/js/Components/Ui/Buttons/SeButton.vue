<template>
    <component
        :is="as"
        type="button"
        :class="classes"
        class="border shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-2 disabled:opacity-50 inline-flex items-center justify-center gap-2"
    >
        <transition
            enter-active-class="transition-all duration-100 ease-out"
            enter-from-class="!w-0 opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-75 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="!w-0 opacity-0"
        >
            <div v-if="$slots.icon" class="w-5 h-[19px] my-auto">
                <slot name="icon"></slot>
            </div>
        </transition>
        <slot></slot>
    </component>
</template>
<script>
export default {
    props: {
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl', ].includes(value);
            },
        },
        rounded: {
            type: String,
            default: 'full', // todo ButtonGroups [leftButton, middleButton, rightButton] needs a way of specifing borders? right now you can get double borders :S (see tables)
        },
        as: {
            type: String,
            default: 'button',
        },
        fontWeight:{
            type:String,
            default:'font-medium'
        }
    },
    computed: {
        classes() {
            const sizes = {
                custom: '', // byop bring your own padding.
                xxs: 'py-0.5 px-1 text-sm',
                xs: 'p-1.5',
                sm: 'py-2 px-3',
                md: 'py-2 px-4',
                lg: 'py-2 px-5',
                xl: 'py-3 px-6',
            };

            const corners = {
                full: 'rounded-md',
                none: 'rounded-none',
                left: 'rounded-md rounded-r-none',
                right: 'rounded-md rounded-l-none',
            };

            const classes = [];

            if (sizes[this.size]) {
                classes.push(sizes[this.size]);
            }

            if (corners[this.rounded]) {
                classes.push(corners[this.rounded]);
            }

            if (this.$attrs.disabled && this.as == 'a') { //normal disabled doesn't work if you're using button as an a tag
                classes.push('pointer-events-none opacity-50');
            }

            classes.push(this.fontWeight);


            return classes;
        },
    },
};
</script>
