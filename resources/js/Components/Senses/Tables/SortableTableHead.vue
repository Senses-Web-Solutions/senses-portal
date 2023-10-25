<template>
    <th class="relative overflow-hidden bg-white" :style="{ width: thWidth }">
        <Popover
            v-slot="{ open }"
            :style="{ width: buttonWidth }"
        >
            <PopoverButton
                ref="popoverButton"

                class="w-full px-4 py-table outline-none"
                :disabled="!filterable && !headerColumn.sort"
            >
                <div class="flex items-center justify-between" :class="{'cursor-pointer': !(!filterable && !headerColumn.sort)}">
                    <p ref="reference" class="capitalize truncate " :class="headerColumn?.filter?.value || headerColumn?.key === sort ? 'text-primary-800 font-bold'  : ''">{{ headerColumn.label.replaceAll('_', ' ') }}</p>
                    <div class="flex items-center" v-if="headerColumn.sort">
                        <template v-if="headerColumn.key === sort">
                            <ArrowUpIcon
                                v-if="order === 'asc'"
                                class="text-primary-800 font-bold group-hover:text-zinc-500 shrink-0 h-3 w-3 mr-0.25"
                            />
                            <ArrowDownIcon
                                v-if="order === 'desc'"
                                class="text-primary-800 font-bold group-hover:text-zinc-500 shrink-0 h-3 w-3 mr-0.25"
                            />
                        </template>
                        <div v-if="headerColumn.filter?.value" class="flex md:mt-0">
                            <FilterIcon class="shrink-0 w-3 h-3 text-zinc-400 group-hover:text-zinc-500" />
                        </div>
                    </div>
                </div>
            </PopoverButton>

            <teleport to="body">
                <div
                    ref="popperTarget"
                    class="absolute z-60"
                >
                    <PopoverTransition @after-leave="destroyPopover">
                        <PopoverPanel
                            v-if="open"
                            static
                            class="right-0 mt-2 font-normal origin-top"
                        >
                            <Card
                                flush
                                class="shadow-md"
                            >
                                <TableColumnFilterPopover
                                    :url="url"
                                    :condensed="condensed"
                                    :disabled="disabled"
                                    :header-column="headerColumn"
                                    :order="order"
                                    :sort="sort"
                                    @close="closePopover"
                                    @filtered="$emit('filtered', $event)"
                                    @sort="$emit('sort', $event)"
                                    @vue:mounted="positionPopover"
                                />
                            </Card>
                        </PopoverPanel>
                    </PopoverTransition>
                </div>
            </teleport>
        </Popover>
    </th>
</template>
<script>
import { FilterIcon, ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/outline';
import { Popover, PopoverPanel, PopoverButton } from '@headlessui/vue';
import { nextTick, ref } from 'vue';
import { createPopper } from '@popperjs/core';
import Card from '../../Ui/Cards/Card.vue';
import PopoverTransition from '../../Ui/Transitions/PopoverTransition.vue';
import TableColumnFilterPopover from './TableColumnFilterPopover.vue';

export default {
    components: {
        FilterIcon,
        ArrowUpIcon,
        ArrowDownIcon,
        Popover,
        PopoverPanel,
        PopoverButton,
        PopoverTransition,
        Card,
        TableColumnFilterPopover
    },
    props: {
        condensed: {
            type: Boolean,
            default: false,
        },
        headerColumn: {
            type: Object,
            required: true,
        },
        sort: {
            type: String,
            required: true,
        },
        order: {
            type: String,
            required: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        filterable: {
            type: Boolean,
            default: true,
        },
        width:{
            default: null,
        },
        url:{
            type:String,
        }
    },
    emits: ['sort', 'filtered'],
    setup () {

        const popperTarget = ref(null);
        const popoverButton = ref(null);
        const reference = ref(null);
        let popperInstance = null;

        const positionPopover = () => {
            nextTick(() => {
                popperInstance = createPopper(reference.value, popperTarget.value, {
                    placement: 'bottom'
                });
            });
        }

        function closePopover() {
            popoverButton.value.$el.click();
        }

        function destroyPopover() {
            popperInstance?.destroy();
        }

        return { popperTarget, popoverButton, positionPopover, closePopover, destroyPopover, reference }
    },
    computed:{
        thWidth() {
            return this.width ? this.width : null
        },
        buttonWidth() {
            if( this.thWidth && typeof this.thWidth === 'string' && this.thWidth.includes('%')) {
                return null;
            }

            return this.thWidth;
        }
    }
};
</script>
