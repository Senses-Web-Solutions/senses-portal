<template>
    <div
        ref="reference"
        class="relative"
        @mouseenter="mouseEnter"
        @mouseleave="mouseLeave"
    >
        <div class="inline outline-none cursor-pointer">
            <slot></slot>
        </div>
        <teleport to="body">
            <div ref="tooltip" class="absolute z-60">
                <PopoverTransition
                    @after-leave="destroyPopper"
                    @enter="initializePopper"
                >
                    <div
                        v-if="active && $slots.content"
                        class="z-60 right-0 origin-top"
                    >
                        <Card flush class="shadow-md">
                            <div class="w-80 p-3 space-y-2">
                                <slot name="content"></slot>
                            </div>
                        </Card>
                    </div>
                </PopoverTransition>
            </div>
        </teleport>
    </div>
</template>
<script>
import { ref } from 'vue';
import { createPopper } from '@popperjs/core';
import PopoverTransition from './Transitions/PopoverTransition.vue';
import Card from './Cards/Card.vue';

export default {
    components: {
        PopoverTransition,
        Card
    },
    setup(props, {emit}) {
        const active = ref(false);
        const tooltip = ref(null);
        const reference = ref(null);
        let popperInstance = null;

        function initializePopper() {
            if (popperInstance) {
                return;
            }
            popperInstance = createPopper(reference.value, tooltip.value, {
                placement: 'bottom',
            });
        }

        function destroyPopper() {
            popperInstance?.destroy();
            popperInstance = null;
        }

        function mouseEnter() {
            active.value = true;
            emit('mouseEnter');
        }

        function mouseLeave() {
            active.value = false;
            emit('mouseLeave');
        }

        return { active, tooltip, reference, destroyPopper, initializePopper, mouseEnter, mouseLeave };
    },
};
</script>
