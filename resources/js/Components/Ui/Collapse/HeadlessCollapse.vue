<template>
    <div>
        <div @click="disableClickEvent ? null : toggle()">
            <slot name="header" :is-open="isOpen" />
        </div>
        <CollapseTransition>
            <template v-if="hideMethod === 'v-show'">
                <div v-show="isOpen">
                    <slot />
                </div>
            </template>
            <template v-else>
                <div v-if="isOpen">
                    <slot />
                </div>
            </template>
        </CollapseTransition>
    </div>
</template>
<script>
import CollapseTransition from "../Transitions/CollapseTransition.vue";

export default {
    components: {
        CollapseTransition,
    },
    props: {
        disableClickEvent: {
            type: Boolean,
            default: false
        },
        open: {
            type: Boolean,
            default: false,
        },
        hideMethod: {
            type: String,
            default: 'v-if'
        }
    },
    data () {
        const isOpen = this.open;
        return {
            isOpen,
        };
    },
    watch: {
        open (v) {
            this.isOpen = v;
        },
    },
    methods: {
        toggle () {
            this.isOpen = !this.isOpen;
        },
    },
};
</script>
