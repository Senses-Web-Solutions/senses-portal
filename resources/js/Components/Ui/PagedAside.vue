<template>
    <div>
        <Flipper
            :flip-key="currentPageIndex"
            :spring="{
                stiffness: 987,
                damping: 63,
                dampingRatio: 1,
                frequencyResponse: 0.2,
            }"
        >
            <component
                v-bind="{ ...$props, ...$attrs }"
                :is="
                    pages[currentPageIndex]?.component?.value
                        ? pages[currentPageIndex]?.component.value
                        : pages[currentPageIndex]?.component
                "
                :data="data"
                :error="error"
                @update:data="$emit('update:data', $event)"
                @first="currentPageIndex = 0"
                @prev="prev"
                @next="next"
            ></component>
        </Flipper>
        <slot v-bind="{ next, prev, first, last, current }"></slot>
    </div>
</template>
<script>
import { computed, ref } from "vue";
import FadeTransition from "./Transitions/FadeTransition.vue";
import Flipper from "./Flip/Flipper.vue";

export default {
    components: {
        FadeTransition,
        Flipper,
    },
    props: {
        pages: {
            type: Array,
            required: true,
        },
        data: {
            type: Object,
            required: true,
        },
    },
    emits: ['update:data'],
    setup(props) {
        const currentPageIndex = ref(0);
        const error = ref(null);

        function next() {
            if (props.pages[currentPageIndex.value].validator) {
                error.value = props.pages[currentPageIndex.value].validator(props.data.form);
                if (error.value.valid && props.pages[currentPageIndex.value + 1]) {
                    currentPageIndex.value += 1;
                }
            } else if (props.pages[currentPageIndex.value + 1]) {
                currentPageIndex.value += 1;
            }
        }

        function prev() {
            if (props.pages[currentPageIndex.value - 1]) {
                currentPageIndex.value -= 1;
            }
        }

        const current = computed(() => currentPageIndex.value + 1);
        const last = computed(() => props.pages.length);
        const first = computed(() => 1);

        return { currentPageIndex, next, prev, error, first, last, current };
    },
};
</script>