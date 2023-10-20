<template>
    <transition
        :enter-active-class="`transition ease-out ${enterDurationClass}`"
        enter-from-class="scale-y-0"
        enter-to-class="scale-y-100"
        :leave-active-class="`transition ease-in ${leaveDurationClass}`"
        leave-from-class="scale-y-100"
        leave-to-class="scale-y-0"
    >
        <slot></slot>
    </transition>
</template>
<script>
export default {
    props: {
        enterDurationClass: {
            type: String,
            default: 'duration-1000'
        },
        leaveDurationClass: {
            type: String,
            default: 'duration-750'
        }
    }
}
</script>