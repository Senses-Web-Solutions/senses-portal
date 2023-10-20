<template>
    <Menu v-slot="{ open }" as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                ref="button"
                class="text-left"
                :class="{'w-full': fullWidth}"
            >
                <DetailBadge
                    clickable
                    has-arrow
                    :full-width="fullWidth"
                    :colour="modelValue?.colour"
                    :loading="state.is(FormState.SUBMITTING)"
                    :no-floating-label="noFloatingLabel"
                >
                    <template #title><slot /></template>
                    {{ modelValue?.title ?? placeholder }}
                </DetailBadge>
            </MenuButton>
        </div>

        <div ref="popperTarget" class="absolute z-50 w-full">
            <PopoverTransition @enter="popoverEnter">
                <MenuItems v-if="open" ref="panel" class="overflow-x-hidden overflow-y-auto max-h-80" :class="{ '!w-full': fullWidth }" static>
                    <template v-if="state.is(FormState.IDLE)">
                        <StaggeredListTransition>
                            <MenuItem v-for="(
                                    option, optionIndex
                                ) in opts"
                                :key="`selectableDetailBadge${option.id}`"
                                :disabled="option.id === modelValue?.id"
                                :class="{ 'bg-primary-100': option.id === modelValue?.id}"
                                dot
                                :dot-colour="`bg-${option.colour}` ?? 'bg-primary-500'"
                                :style="{
                                    '--tw-transition-delay': `${
                                        optionIndex *
                                        TransitionTimings.STAGGERED_LIST_DELAY
                                        }ms`,
                                }" @click="change(option)">
                            {{ option.title }}
                            </MenuItem>
                        </StaggeredListTransition>
                    </template>
                    <template v-else-if="state.is(FormState.LOADING)">
                        <div class="px-3 py-2">
                            <LoadingIcon class="w-8 h-8 mx-auto my-8 text-primary"></LoadingIcon>
                        </div>
                    </template>
                    <template v-else>
                        <div class="px-3 py-2">Something went wrong.</div>
                    </template>
                </MenuItems>
            </PopoverTransition>
        </div>
    </Menu>
</template>
<script>
import { createPopper } from '@popperjs/core';
import { Menu, MenuButton } from '@headlessui/vue';
import { ref, nextTick, reactive, watch } from 'vue';
import MenuItems from '../Menu/MenuItems.vue';
import MenuItem from '../Menu/MenuItem.vue';
import DetailBadge from './DetailBadge.vue';
import PopoverTransition from '../Transitions/PopoverTransition.vue';
import CachedRequest from '../../../Support/CachedRequest';
import FormState from '../../../States/FormState';
import LoadingIcon from '../LoadingIcon.vue';
import StaggeredListTransition from '../Transitions/StaggeredListTransition.vue';
import TransitionTimings from '../../../Enums/TransitionTimings';

export default {
    components: {
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        DetailBadge,
        PopoverTransition,
        LoadingIcon,
        StaggeredListTransition,
    },
    props: {
        modelValue: {
            type: [Object, null],
            required: true,
        },
        url: {
            type: String,
            required: false,
            default: ''
        },
        options: {
            type: Array,
            required: false,
            default: null
        },
        noFloatingLabel: {
            type: Boolean,
            default: false
        },
        fullWidth: {
            type: Boolean,
            default: false
        },
        placeholder: {
            type: String,
            default: 'Select a value'
        }
    },
    emits: ['update:modelValue'],
    setup (props, { emit }) {
        const state = reactive(new FormState());
        const opts = ref(props.options || []);

        function load () {
            state.set(FormState.LOADING);
            CachedRequest.get(
                props.url
            )
                .then((response) => {
                    opts.value = response.data;
                    state.set(FormState.IDLE);
                })
                .catch(() => state.set(FormState.ERROR));
        }

        watch(
            () => props.options,
            () => {
                opts.value = props.options;
            });



        const button = ref(null);
        const popperTarget = ref(null);

        function positionPopover () {
            nextTick(() => {
                createPopper(button.value.el, popperTarget.value, {
                    placement: 'bottom-start',
                });
            });
        }

        function popoverEnter () {
            positionPopover();
            if (props.url) {
                load();
            }
        }

        function change (value) {
            emit('update:modelValue', value);
        }

        return {
            positionPopover,
            popperTarget,
            button,
            popoverEnter,
            FormState,
            state,
            opts,
            change,
            TransitionTimings,
        };
    },
};
</script>
