<template>
    <span
        class="inline-block overflow-hidden rounded-full"
        :class="[`bg-${user.colour}`, fluidSize ? '' : 'w-8 h-8']"
    >
        <Transition
            appear
            enter-active-class="transition duration-500 delay-variable"
            enter-from-class="translate-y-full"
            enter-to-class="translate-y-0"
            leave-active-class="transition duration-500 delay-variable"
            leave-to-class="translate-y-full"
            leave-from-class="translate-y-0"
        >
            <svg
                class="w-full h-full opacity-30"
                :class="[`text-${user.text_colour}`]"
                fill="currentColor"
                viewBox="0 0 24 24"
                :style="{
                    '--tw-transition-delay': `${index * 200}ms`,
                }"
            >
                <path
                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
                />
            </svg>
        </Transition>
    </span>
</template>
<script>
// todo merge with user icon as a prop, don't really need two circles and having to decide which one to show
export default {
    props: {
        user: {
            type: Object,
            required: true,
        },
        index: {
            type: Number,
            required: false,
            default: 0
        },
        fluidSize: {
            type: Boolean,
            required: false,
            default: false
        },
    },
};
</script>
