<template>
    <TransitionGroup
        ref="transitionGroup"
        appear
        tag="div"
        enter-active-class="transition duration-200 delay-variable"
        enter-from-class="-translate-x-6 opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="absolute left-0 right-0 transition duration-150"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="-translate-x-6 opacity-0"
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
                    // Automatically puts the transition delay CSS property on the child elements so they stagger automatically
                    // It may be worth limiting the delay to a certain max count (20 * STAGGERED_LIST_DELAY) for long lists
                    el.style.setProperty('--tw-transition-delay', `${elIndex * TransitionTimings.STAGGERED_LIST_DELAY}ms`);
                });
            })
        }
    }
}
</script>
// :style="{ '--tw-transition-delay': `${index * TransitionTimings.STAGGERED_LIST_DELAY}ms` }"