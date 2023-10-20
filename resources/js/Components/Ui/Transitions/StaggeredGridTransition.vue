<template>
    <TransitionGroup
        ref="transitionGroup"
        appear
        tag="div"
        enter-active-class="transition duration-200 delay-variable relative z-30"
        enter-from-class="scale-75 opacity-0"
        enter-to-class="scale-100 opacity-100"
    >
        <slot/>
    </TransitionGroup>
</template>
<script>
import TransitionTimings from '../../../Enums/TransitionTimings';

export default {
    props: {
        automateChildDelay: {
            type: Boolean,
            default: false
        }
    },
    mounted() {
        if (this.automateChildDelay) {
            this.$nextTick(() => {
                const els = this.$refs.transitionGroup.$el.children;
                Array.from(els).forEach((el, elIndex) => {
                    el.style.setProperty('--tw-transition-delay', `${elIndex * TransitionTimings.STAGGERED_LIST_DELAY}ms`);
                });
            })
        }
    }
}
</script>
// :style="{ '--tw-transition-delay': `${index * TransitionTimings.STAGGERED_LIST_DELAY}ms` }"