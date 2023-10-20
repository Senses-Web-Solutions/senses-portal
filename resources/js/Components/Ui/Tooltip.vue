<template>
    <div
        ref="reference"
        class="relative"
        @mouseenter="active = true"
        @mouseleave="active = false">
        <slot></slot>
        <teleport to="body">
            <div ref="tooltip" class="z-60">
                <PopoverTransition @after-leave="destroyPopper" @enter="initializePopper">
                    <div v-if="active && $slots.content && showTooltip" class="z-60 px-2 py-1 mt-1 text-white bg-zinc-700 rounded shadow" :class="hasMaxWidth ? 'max-w-48' : ''">
                        <slot name="content"></slot>
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

export default {
    components: {
        PopoverTransition,
    },

    props: {
        hasMaxWidth: {
            type: Boolean,
            default: true,
        },
        showTooltip: {
            type: Boolean,
            default: true,
        }
    },

    setup() {
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

        return {
            active,
            tooltip,
            reference,
            destroyPopper,
            initializePopper
        };
    },
};

</script>
