<template>
    <Popover v-slot="{ open }" class="relative inline-block">
        <PopoverButton ref="popoverButton" class="inline outline-none cursor-pointer" @click.stop>
            <slot></slot>
        </PopoverButton>

        <teleport to="body">
            <div ref="popperTarget" class="absolute z-60">
                <PopoverTransition>
                    <PopoverPanel v-if="open" static class="right-0 origin-top">
                        <Card flush class="shadow-md">
                            <div ref="element" class="w-64 p-3 space-y-2" @vue:mounted="positionPopover">
                                <slot name="content"></slot>
                            </div>
                        </Card>
                    </PopoverPanel>
                </PopoverTransition>
            </div>
        </teleport>
    </Popover>
</template>
<script>
import { createPopper } from '@popperjs/core';
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { nextTick, ref } from 'vue';
import Card from './Cards/Card.vue';
import PopoverTransition from './Transitions/PopoverTransition.vue';

export default {
    components: { Popover, PopoverButton, PopoverPanel, Card, PopoverTransition },
    props: {
        id: {
            type: [String, Number],
            required: false,
            default: null
        }
    },
    setup () {
        const popperTarget = ref(null);
        const popoverButton = ref(null);

        const positionPopover = () => {
            nextTick(() => {
                createPopper(popoverButton.value.$el, popperTarget.value, {
                    placement: 'bottom'
                });
            });
        }

        return { popperTarget, popoverButton, positionPopover };
    },
};
</script>
