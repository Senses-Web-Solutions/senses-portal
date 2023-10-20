<template>
    <Popover v-if="id" v-slot="{ open }" class="relative inline-block">
        <component :is="tooltip ? 'Tooltip' : 'div'">
            <PopoverButton ref="popoverButton" class="inline outline-none cursor-pointer" @click.stop>
                <slot></slot>

            </PopoverButton>
            <template #content>
                {{ user?.full_name ?? title }}
            </template>
        </component>

        <teleport to="body">
            <div ref="popperTarget" class="absolute z-60">
                <PopoverTransition>
                    <PopoverPanel v-if="open" static class="right-0 origin-top">
                        <Card flush class="shadow-md">
                            <UserPopoverContent :id="id" @vue:mounted="positionPopover" />
                        </Card>
                    </PopoverPanel>
                </PopoverTransition>
            </div>
        </teleport>
    </Popover>
    <button v-else class="inline">
        <slot></slot>
    </button>
</template>
<script>
import { createPopper } from '@popperjs/core';
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { nextTick, ref } from 'vue';
import PopoverTransition from '../../Ui/Transitions/PopoverTransition.vue';
import Card from "../../Ui/Cards/Card.vue";
import UserPopoverContent from './UserPopoverContent.vue';
import Tooltip from '../../Ui/Tooltip.vue';

export default {
    components: { Popover, Tooltip, PopoverButton, PopoverPanel, Card, UserPopoverContent, PopoverTransition },
    props: {
        id: {
            type: [String, Number],
            required: false,
            default: null
        },
        title: {
            type: String,
            default: ''
        },
        user: {
            type: Object,
            default: null
        },
        tooltip: {
            type: Boolean,
            default: false
        }
    },
    setup() {
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
