<template>
    <div class="p-5 w-80">

        <div :class="sortable || filterable ? 'divide-y divide-zinc-200' : 'divide-y divide-zinc-200'">
            <div class="py-3 first:pt-0 last:pb-0" v-if="sortable">
                <!-- Active: "bg-zinc-100", Not Active: "text-zinc-700" -->
                <div class="flex justify-between items-center">
                    <a id="menu-item-0" href="#" class="block px-2 py-2 text-zinc-700" :class="order === 'asc' && sort === headerColumn.key ? 'font-semibold' : ''" role="menuitem" tabindex="-1" @click="disabled || applySort('asc')">Sort Ascending</a>
                    <SortAscendingIcon class="text-zinc-700 w-5 h-5"/>
                </div>
                <div class="flex justify-between items-center">
                    <a id="menu-item-1" href="#" class="block px-2 py-2 text-zinc-700" :class="order === 'desc' && sort === headerColumn.key ? 'font-semibold' : ''" role="menuitem" tabindex="-1" @click="disabled || applySort('desc')">Sort Descending</a>
                    <SortDescendingIcon class="text-zinc-700 w-5 h-5"/>
                </div>
            </div>
            <div v-if="!sortable && filterable">
                <p class="text-zinc-600 px-2 pb-3 text-center">This column is not sortable.</p>
            </div>

            <div v-if="!condensed && filterable" class="px-2 py-3 first:pt-0 last:pb-0">
                <TableFilter v-model="filter" :field-key="headerColumn.key" :url="url"/>
            </div>

            <div v-if="!filterable && sortable">
                <p class="text-zinc-600 px-2 py-3 text-center">This column is not filterable.</p>
            </div>

            <div v-if="!filterable && !sortable">
                <p class="text-zinc-600 text-center block -mx-3">This column is not filterable or sortable.</p>
            </div>

            <div class="flex justify-end py-3 space-x-3 first:pt-0 last:pb-0" v-if="sortable || filterable">
                <SecondaryButton type="button" @click="$emit('close')">
                    Cancel
                </SecondaryButton>
                <PrimaryButton type="submit" @click="$emit('filtered',  filter);$emit('close')">
                    Done
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

<script>
import TableFilter from './Filters/TableFilter.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import { SortAscendingIcon, SortDescendingIcon } from "@heroicons/vue/outline";

export default {
    components: {
        TableFilter,
        PrimaryButton,
        SecondaryButton,

        SortAscendingIcon,
        SortDescendingIcon,
    },
    props: {
        condensed: {
            type: Boolean,
            default: false,
        },
        order: {
            type: String,
            required: true,
        },
        sort: {
            type: String,
            required: true,
        },
        headerColumn: {
            type: Object,
            required: true,
        },
        url:{
            type:String
        }
    },
    emits: ['sort', 'filtered', 'close'],
    data() {
        return {
            filter: JSON.parse(JSON.stringify(this.headerColumn.filter)),
        };
    },
    computed: {
        sortable() {
            return this.headerColumn.sort;
        },
        filterable() {
            return this.headerColumn.filter;
        }
    },
    methods: {
        applySort(direction) {
            this.$emit('sort', direction);
            this.$emit('close');
        }
    },
};
</script>
