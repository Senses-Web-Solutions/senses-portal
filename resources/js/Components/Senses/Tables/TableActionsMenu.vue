<template>
    <Menu v-slot="{ open }" as="div" class="relative inline-block text-left z-30">
        <div>
            <MenuButton ref="button" class="text-left">
                    <SecondaryButton :disabled="disabled" :rounded="rounded" title="Apply Actions">
                        <Icon icon="la:hand-pointer" class="w-5 h-5" />
                    </SecondaryButton>
            </MenuButton>
        </div>

        <teleport to="body">
            <div ref="popperTarget" class="absolute z-50">
                <PopoverTransition @enter="popoverEnter">
                    <MenuItems
                        v-if="open"
                        ref="panel"
                        class="max-h-80 overflow-y-auto overflow-x-hidden"
                        static
                    >
                        <StaggeredListTransition>
                            <MenuItem
                                v-for="(option, optionIndex) in actions"
                                :key="`selectableDetailBadge${option.id}`"
                                :style="{
                                    '--tw-transition-delay': `${
                                        optionIndex *
                                        TransitionTimings.STAGGERED_LIST_DELAY
                                    }ms`,
                                }"
                                @click="select(option)"
                            >
                                <template #icon>
                                    <component :is="option.icon" />
                                </template>
                                {{ option.title }}
                            </MenuItem>
                        </StaggeredListTransition>
                    </MenuItems>
                </PopoverTransition>
            </div>
        </teleport>
    </Menu>
</template>
<script>
import { createPopper } from '@popperjs/core';
import { Menu, MenuButton } from '@headlessui/vue';
import { ref, nextTick } from 'vue';
import MenuItems from '../../Ui/Menu/MenuItems.vue';
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import PopoverTransition from '../../Ui/Transitions/PopoverTransition.vue';
import StaggeredListTransition from '../../Ui/Transitions/StaggeredListTransition.vue';
import TransitionTimings from '../../../Enums/TransitionTimings';
import { CollectionIcon } from "@heroicons/vue/outline";

import {
    Icon
} from '@iconify/vue';

export default {
    components: {
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        PopoverTransition,
        StaggeredListTransition,
        SecondaryButton,
        CollectionIcon,
        Icon,
    },
    props: {
        actions: {
            type: Array,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        rounded: {
            type: String,
            default: 'full'
        }
    },
    emits: ['selected'],
    setup(_, { emit }) {
        const button = ref(null);
        const popperTarget = ref(null);

        function positionPopover() {
            nextTick(() => {
                createPopper(button.value.el, popperTarget.value, {
                    placement: 'bottom-start',
                });
            });
        }

        function popoverEnter() {
            positionPopover();
        }

        function select(option) {
            emit('selected', option);
        }

        return { button, popperTarget, positionPopover, popoverEnter, select, TransitionTimings };
    },
};
</script>
