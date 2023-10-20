<template>
    <div class="relative" :id="table + '-table'">
        <CollapseTransition>
            <component :is="as" :external-title="externalTitle" :flush="true">
                <template v-if="as.toLowerCase() == 'card'" #title>{{ title }}</template>
                <template v-if="!condensed && as.toLowerCase() !== 'card'">
                    <div class="flex items-center space-x-3">
                        <ActiveTableFilters :filtered-fields="filteredFields" @clearFilter="clearFilter" />

                        <SecondaryButton v-if="!dropdownButtons" @click="resetTableUserSetting">
                            <Icon icon="carbon:filter-remove" class="w-5 h-5" />
                        </SecondaryButton>

                        <span class="relative z-10 inline-flex rounded-md shadow-sm">
                            <SecondaryButton v-if="showTemplates" :rounded="hasSetting || hasActions > 0
                                        ? 'left'
                                        : 'full'" :disabled="isFailed" @click="openTableTemplatesMenu">Templates</SecondaryButton>
                            <SecondaryButton :rounded="showTemplates && (!hasSetting || hasActions == 0)
                                        ? 'right'
                                        : 'full'" :disabled="isFailed" @click="openColumnsMenu">Columns</SecondaryButton>
                            <TableActionsMenu
                                v-if="hasActions"
                                :disabled="isFailed || !isSelecting"
                                :actions="actions"
                                @selected="applyAction"></TableActionsMenu>
                        </span>

                        <slot name="actions"></slot>

                        <SecondaryButton v-if="hasActions" @click="selecting = !selecting" title="Select Multiple">
                            <Icon icon="ant-design:check-square-outlined" class="w-5 h-5" />
                        </SecondaryButton>

                        <SecondaryButton v-if="isExportable" :disabled="isFailed" @click="exportTable" title="Export to Excel">
                            <DownloadIcon class="w-5 h-5 text-black shrink-0 group-hover:text-black" />
                        </SecondaryButton>

                        <SeInput
                            v-if="isSearchable"
                            :id="table + '-search'"
                            v-model="search"
                            :disabled="isFailed"
                            class="inline-flex items-center"
                            name="search"
                            placeholder="Search"
                            @input="searchFilter" />
                    </div>
                </template>
                <template v-if="!condensed && as.toLowerCase() === 'card'" #actions>
                    <ActiveTableFilters :filtered-fields="filteredFields" @clearFilter="clearFilter" />

                    <slot name="actions"></slot>

                    <span class="relative z-10 inline-flex rounded-md shadow-sm">
                        <TableActionsMenu
                            rounded="left"
                            class="translate-x-[2px]"
                            v-if="dropdownButtons && hasActions && isSelecting"
                            :disabled="isFailed"
                            :actions="actions"
                            @selected="applyAction"/>

                        <SecondaryButton class="translate-x-[1px]" :rounded="(hasActions && isSelecting) ? 'none' : 'left'" v-if="hasActions" @click="selecting = !selecting" title="Select Multiple">
                            <Icon icon="ant-design:check-square-outlined" class="w-5 h-5" />
                        </SecondaryButton>

                        <SecondaryButton :rounded="hasActions ? 'right' : 'full'" v-if="isExportable" :disabled="isFailed" @click="exportTable" title="Export to Excel">
                            <DownloadIcon class="w-5 h-5 text-black shrink-0 group-hover:text-black" />
                        </SecondaryButton>
                    </span>

                    <div class="z-20">
                        <SecondaryButton
                            class="mr-3"
                            rounded="full"
                            title="Settings"
                            @click="this.$asides.push('SeTableMenuAside', {
                                table: this,
                                setting: this.setting,
                                currentTemplateName: this.currentTemplateName
                            })">
                            <CogIcon class="w-5 h-5" />
                        </SecondaryButton>
                        <SeInput v-if="isSearchable" :id="table + '-search'" v-model="search" :disabled="isFailed" class="inline-flex items-center" name="search" placeholder="Search"
                            @keydown.space.stop @input="searchFilter">
                            <template #icon>
                                <SearchIcon class="w-5 h-[20px] mx-auto" />
                            </template>
                        </SeInput>
                    </div>

                    <PlusButton
                        v-if="plusButton"
                        :disabled="plusButton.disabled"
                        :form="plusButton.form"
                        :data="plusButton.data">
                        <PlusIcon class="w-4 h-[20px] mx-auto" />
                    </PlusButton>
                </template>

                <template v-if="condensed" #actions>
                    <slot name="actions"></slot>
                </template>

                <CollapseTransition>
                    <IndeterminateLoadingBar v-if="loading" />
                </CollapseTransition>

                <div v-if="initialLoad">
                    <div class="relative flex flex-col" :id="tableWrapperId">
                        <div v-if="isEmpty">
                            <slot name="table-empty">
                                <EmptyState :item="tableNameFriendly"></EmptyState>
                            </slot>
                        </div>
                        <div v-else class=" overflow-x-auto">
                            <div class="min-w-full align-middle overflow-hidden">
                                <div
                                    ref="tableWrapper"
                                    v-scroll="onTableScroll"
                                    class="overflow-x-auto"
                                    :class="{
                                        'border-b border-zinc-200': hasFooter && !slotEmpty($slots['footer']),
                                        'rounded-t-lg': rounded
                                    }"
                                    :style="{
                                        'max-height': height === true ? viewportHeightCss : null
                                    }">
                                    <table class="rounded-lg min-w-full divide-y divide-zinc-200 dark:divide-zinc-800" :id="tableId">
                                        <thead class="sticky top-0 bg-white z-10 transition" :class="{'shadow': tableScroll > 0}">
                                            <tr>
                                                <th class="px-4 py-table overflow-hidden whitespace-nowrap text-left text-zinc-500 first:rounded-tl-lg last:rounded-tr-lg w-12 bg-white" v-if="rowExternalLinks || isSelecting">
                                                    <SeCheckbox
                                                        v-if="isSelecting"
                                                        id="row_all"
                                                        class="mx-auto"
                                                        name="row_all"
                                                        @update:modelValue="
                                                            toggleSelectedRows($event)
                                                        " />
                                                </th>
                                                <template v-for="(
                                                        headerColumn, index
                                                    ) in renderFields" :key="headerColumn.key">
                                                    <slot
                                                        :col="headerColumn"
                                                        :index="index"
                                                        :name=" headerColumn.key + '_header'"
                                                        class="px-4 py-table text-left whitespace-nowrap text-zinc-500"
                                                        :click="sortByField"
                                                        :width="headerColumn.width">
                                                        <SortableTableHead
                                                            class="text-left whitespace-nowrap text-zinc-500 font-medium"
                                                            :header-column="headerColumn"
                                                            :url="currentUrl"
                                                            :sort="sort"
                                                            :order="order"
                                                            :width="headerColumn.width"
                                                            :disabled="isFailed"
                                                            :filterable="isFilterable"
                                                            :condensed="condensed"
                                                            @sort="sortByField(headerColumn.key, $event)"
                                                            @filter="showFilter(headerColumn)"
                                                            @filtered="applyFilter(headerColumn, $event)"
                                                        />
                                                    </slot>
                                                </template>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                            <tr
                                                v-for="(row, index) in rowData"
                                                :ref="'row-' + index"
                                                :key="`row${row.id}`"
                                                class="divide-x divide-zinc-200 dark:divide-zinc-800"
                                                :class="{
                                                    'bg-white': !row.row_colour && index % 2 === 0,
                                                    'bg-zinc-50': !row.row_colour && index % 2 !== 0,
                                                    'hover:bg-zinc-50': !row.row_colour && interactable,
                                                    'cursor-pointer': interactable
                                                    },
                                                    rowID(row),
                                                    row.row_colour ? 'bg-' + row.row_colour : null
                                                "
                                                :style="{
                                                    '--tw-transition-delay': `${
                                                        index * 25
                                                    }ms`,
                                                }"
                                                @click="$emit('rowClick', row)">
                                                <td
                                                    v-if="!isSelecting && rowExternalLinks"
                                                    class="h-[44px] overflow-hidden items-center h-full px-2 py-table text-md text-zinc-500 whitespace-nowrap min-w-12 max-w-12"
                                                    :class="{'rounded-bl-lg': as.toLowerCase() == 'card' && !hasFooter && slotEmpty($slots['footer']) }"
                                                    @click.stop="
                                                        $emit('rowClick', row, 'blank')
                                                    ">
                                                    <DotsHorizontalIcon class="w-5 h-5 mx-auto" />
                                                </td>
                                                <td v-if="isSelecting" class="h-[44px] px-2 py-2 text-xl text-black whitespace-nowrap min-w-8 max-w-8" @click.stop="() => {}">
                                                    <SeCheckbox
                                                        :id="'row_' + index"
                                                        class="mx-auto"
                                                        :name="'row_' + index"
                                                        :model-value="
                                                            selectedRowIndexes.includes(
                                                                index
                                                            )
                                                        "
                                                        @update:modelValue="
                                                            rowSelect(
                                                                $event,
                                                                row,
                                                                index
                                                            )
                                                        " />
                                                </td>
                                                <slot :row="row" :index="index" name="row">
                                                    <template v-for="(
                                                            field, fieldIndex
                                                        ) in renderFields" :key="field.key">
                                                        <slot
                                                            :col="field"
                                                            :row="row"
                                                            :index="fieldIndex"
                                                            :name="field.key"
                                                            :class="
                                                        columnClass(
                                                            field,
                                                            fieldIndex,
                                                            index
                                                        )
                                                    ">
                                                            <td :class="
                                                            columnClass(
                                                                field,
                                                                fieldIndex,
                                                                index
                                                            )
                                                        ">
                                                                {{
                                                                    formatField(
                                                                        row,
                                                                        field
                                                                    )
                                                                }}
                                                            </td>
                                                        </slot>
                                                    </template>
                                                </slot>
                                            </tr>
                                            <!-- <tr v-if="isEmpty">
                                        <td
                                            :colspan="renderFields.length + 1"
                                            :class="columnClass(null, 1)"
                                            class="py-0 text-center"
                                        >
                                            <slot name="table-empty">
                                                <EmptyState
                                                    :item="tableNameFriendly"
                                                ></EmptyState>
                                            </slot>
                                        </td>
                                    </tr> -->
                                            <tr v-if="rowError">
                                                <td :colspan="renderFields.length + 1" :class="columnClass(null, 1, 1)" class="text-center space-y-4">
                                                    <p class="text-md text-danger-600">
                                                        {{ rowError }}
                                                    </p>
                                                    <SecondaryButton size="xs" @click="resetTableUserSetting" v-if="hasSetting">Reset Table Filters</SecondaryButton>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="hasFooter" class="flex flex-wrap items-center justify-between px-4 py-1 max-w-screen">
                        <p v-if="rows" class="my-2 text-black">
                            Showing <strong>{{ rows.from ?? 0 }}</strong> to
                            <strong>{{ rows.to ?? 0 }}</strong> of
                            <strong>{{ totalRows }}</strong> results
                        </p>

                        <div v-if="hasPagination" class="flex justify-between flex-1 sm:justify-end">
                            <pagination :rows="rows" @page="loadData"></pagination>
                        </div>
                    </div>

                    <slot v-if="!hasFooter && slotEmpty($slots['footer'])">
                        <slot name="footer"></slot>
                    </slot>
                </div>
            </component>
        </CollapseTransition>
    </div>
</template>

<script>
import MenuHeader from '../../Ui/Menu/MenuHeader.vue';
import MenuDivider from '../../Ui/Menu/MenuDivider.vue';
import axios from 'axios';
// axios.interceptors.response.use(function (response) {
//     // Any status code that lie within the range of 2xx cause this function to trigger
//     // Do something with response data
//     return response;
//   }, function (error) {
//     // Any status codes that falls outside the range of 2xx cause this function to trigger
//     // Do something with response error
//     return Promise.reject(error);
//   });
import {
    debounce,
    capitalize,
    clone
} from 'lodash-es';
import draggable from 'vuedraggable';
import {
    vScroll
} from '@vueuse/components';
import slotEmpty from "../../../Support/slotEmpty";
import {
    FilterIcon,
    ArrowDownIcon,
    ArrowUpIcon,
    DownloadIcon,
    SearchIcon,
    XCircleIcon,
    ChevronRightIcon,
    ExternalLinkIcon,
    PlusIcon,
    DotsHorizontalIcon,
    CogIcon,
    TrashIcon,
    CheckCircleIcon,
    CollectionIcon,
    TemplateIcon,
    MinusIcon,
    MenuAlt4Icon,
    ViewListIcon,
    TableIcon,
    MenuIcon,
    ArrowsExpandIcon,
} from '@heroicons/vue/outline';
import {
    CheckCircleIcon as SolidCheckCircleIcon,
    ViewGridIcon,
} from '@heroicons/vue/solid';
// import {
//     MenuItem
// } from '@headlessui/vue';
import {
    Icon
} from '@iconify/vue';
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import highlight from '../../../Support/highlight';
// import ReplyReversedIcon from '../../Icons/ReplyReversed.vue';
import EventHub from '../../../Support/EventHub';
import TableFilter from './Filters/TableFilter.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import InfoButton from '../../Ui/Buttons/InfoButton.vue';
import Pagination from '../Common/Pagination.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeCheckbox from '../../Ui/Inputs/SeCheckbox.vue';
import Card from '../../Ui/Cards/Card.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import ActiveTableFilters from './ActiveTableFilters.vue';
import SortableTableHead from './SortableTableHead.vue';
import Tooltip from '../../Ui/Tooltip.vue';

import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import CollapseTransition from '../../Ui/Transitions/CollapseTransition.vue';
import FadeTransition from '../../Ui/Transitions/FadeTransition.vue';
import FormatDateTime from '../../../Filters/FormatDateTime';
import FormatDate from '../../../Filters/FormatDate';
import Currency from '../../../Filters/Currency';
import EmptyState from '../../Ui/EmptyState.vue';
import SeMenu from '../../Ui/Menu/SeMenu.vue';
import TransitionTimings from '../../../Enums/TransitionTimings';
import PlusButton from '../../Ui/Buttons/PlusButton.vue';
import TableActionsMenu from './TableActionsMenu.vue';
import user from '../../../Support/user';
import useViewportHeightCss from '../../../Support/useViewportHeightCss';

import { isClient } from '../../../Support/client';

// todo loading, make it obvious, also show errors
// todo replace search with input + magnifying icon
// todo add table options for easy column display types (e.g always show as formatted date etc)
// todo add boolean filter option
// todo table widths currently are % but for some reason require table width to be set, its been calculated to be bigger
// todo reduce topbar templates to one template
export default {
    components: {
    EmptyState,
    IndeterminateLoadingBar,
    CollapseTransition,
    Pagination,
    SeSelect,
    SeInput,
    PrimaryButton,
    InfoButton,
    SecondaryButton,
    TableFilter,
    FilterIcon,
    ArrowDownIcon,
    ArrowUpIcon,
    ActiveTableFilters,
    draggable,
    DownloadIcon,
    ViewGridIcon,
    // ReplyReversedIcon,
    ExternalLinkIcon,
    DotsHorizontalIcon,
    SearchIcon,
    SortableTableHead,
    SeCheckbox,
    Card,
    XCircleIcon,
    ChevronRightIcon,
    FadeTransition,
    Tooltip,
    LoadingIcon,
    SeMenu,
    MenuItem,
    PlusButton,
    PlusIcon,
    TableActionsMenu,
    Icon,
    CogIcon,
    MenuDivider,
    MenuHeader,
    MinusIcon,
    MenuAlt4Icon,
    ViewListIcon,
    TableIcon,
    CollectionIcon,
    MenuIcon,
    ArrowsExpandIcon,
    SolidCheckCircleIcon,
},
    directives: {
        scroll: vScroll
    },
    props: {
        title: {
            type: String,
            default: null,
        },
        url: {
            type: String,
            required: false,
            default: '',
        },
        rawUrl: {
            type: String,
            required: false,
            default: '',
        },
        exportUrl: {
            type: String,
            required: false,
        },
        fields: {
            type: Array,
            required: true,
        },
        table: {
            type: String,
            required: true,
        },
        event: {
            type: [String, Array],
            default: null,
        },
        actions: {
            type: Array,
            default: () => [],
        },
        setting: {
            type: String,
            default: null,
        },
        exportable: {
            type: Boolean,
            default: true,
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        condensed: {
            type: Boolean,
            default: false,
        },
        showTemplates: {
            type: Boolean,
            default: true,
        },
        externalTitle: {
            type: Boolean,
            default: true,
        },
        as: {
            type: String,
            default: 'Card',
        },
        maintainer: {
            type: String,
            default: null,
        },
        maintainerId: {
            type: Number,
            default: null,
        },
        limit: {
            type: Number,
            default: null,
        },
        allowedFields: {
            type: Object,
            default: () => {},
        },
        load: {
            type: Boolean,
            default: true,
        },
        rowExternalLinks: {
            type: Boolean,
            default: true,
        },
        defaultSort: {
            type: String,
            default: 'id',
        },
        showPagination: {
            type: Boolean,
            default: true,
        },
        dropdownButtons: {
            type: Boolean,
            default: false,
        },
        plusButton: {
            type: Object,
            default: null
        },
        requiredFields: {
            type: Array,
            default: () => ['id']
        },
        height: {
            type: [Number, String, Boolean],
            default: false
        },
        rounded: {
            type: Boolean,
            default: true
        },
        interactable: {
            type: Boolean,
            default: true
        },
    },
    emits: ['update:fields', 'action-applied', 'rowClickformatField', 'loading', 'updateTotalRows', 'rowClick'],
    data() {
        return {
            order: 'desc',
            sort: this.defaultSort,
            currentUrl: this.url,
            rows: null,
            loading: false,
            initialLoad: false,
            userLimit: null,
            selectedFieldFilterKey: null,
            search: null,
            selectedRowIndexes: [],
            action: null,
            currentTemplateName: 'Default',
            templates: {
                Default: null
            },
            rowError: null,
            tableMaintainer: null,
            maintainerBinded: false,
            TransitionTimings,
            fieldVisibility: {},
            selecting: false,
            viewportHeightCss: useViewportHeightCss(112 + 104 + 32),
            tableScroll: 0,
        };
    },
    computed: {
        tableWrapperId() {
            return 'table-' + this.table+'-wrapper';
        },
        tableId() {
            return 'table-' + this.table;
        },
        hiddenFields() {
            const hiddenFields = [];

            this.fields.forEach((field) => {
                if (this.fieldVisibility && this.fieldVisibility[field.key] === false) {
                    hiddenFields.push(field.key);
                }
            });

            return hiddenFields;
        },

        dropdownButtonMenuItems() {
            if (!this.dropdownButtons) {
                return [];
            }

            const buttons = [
                // {
                //     title: 'Templates',
                //     icon: TemplateIcon,
                //     action: this.openTableTemplatesMenu
                // },
                {
                    title: 'Table Settings',
                    icon: TableIcon,
                    action: this.openColumnsMenu
                },
                // {
                //     title: 'Clear Settings',
                //     icon: TrashIcon,
                //     action: this.resetTableUserSetting
                // },

            ];

            if (this.hasActions) {
                buttons.push({
                    title: 'Toggle Selecting',
                    icon: CheckCircleIcon,
                    action: () => { this.selecting = !this.selecting }
                },)
            }

            if (this.isExportable) {
                buttons.push({
                    title: 'Export',
                    icon: DownloadIcon,
                    action: this.exportTable
                })
            }

            // buttons.push({
            //     type: 'divider'
            // });

            // buttons.push({
            //     type: 'header',
            //     title: 'No per page',
            //     align: 'center'
            // });

            // buttons.push({
            //     title: 'Fit to screen',
            //     icon: ArrowsExpandIcon,
            //     action: () => {
            //         this.userLimit = Math.floor((window.innerHeight - 365) / 43);
            //         this.updateUserSetting(true, true);
            //     }
            // });

            // buttons.push({
            //     title: '25',
            //     icon: MinusIcon,
            //     action: () => {
            //         this.userLimit = 25;
            //         this.updateUserSetting(true, true);
            //     }
            // });

            // buttons.push({
            //     title: '50',
            //     icon: MenuAlt4Icon,
            //     action: () => {
            //         this.userLimit = 50;
            //         this.updateUserSetting(true, true);
            //     }
            // });

            // buttons.push({
            //     title: '75',
            //     icon: MenuIcon,
            //     action: () => {
            //         this.userLimit = 75;
            //         this.updateUserSetting(true, true);
            //     }
            // });

            // buttons.push({
            //     title: '100',
            //     icon: ViewListIcon,
            //     action: () => {
            //         this.userLimit = 100;
            //         this.updateUserSetting(true, true);
            //     }
            // });

            return buttons;
            // return buttons.sort((a, b) => a.text.localeCompare(b.text));
        },

        tableNameFriendly() {
            return this.tableName.replaceAll(/[_,-]/g, ' ');
        },

        renderFields() {
            return this.visibleFields;
        },

        renderFieldKeys() {
            return this.renderFields.map((i) => i.key);
        },

        visibleFields() {
            return this.fields.filter((field) =>
                this.isFieldVisible(field.key)
            );
        },

        fieldKeys() {
            return this.fields.map((i) => i.key);
        },

        sortableFields() {
            return this.renderFields.filter((field) => field.sort ?? true);
        },

        filterableFields() {
            return this.renderFields.filter((field) => field.filter ?? true);
        },

        tableName() {
            return this.table;
        },

        filteredFields() {
            return this.filterableFields.filter(
                (field) => field.filter.value !== null
            );
        },

        currentFilters() {
            const filters = [];
            this.filteredFields.forEach((field) => {
                filters.push({
                    field_key: field.key,
                    key: field.filter.key,
                    sub_type: field.filter.sub_type,
                    value: field.filter.value,
                });
            });

            return filters;
        },

        selectedFilterField() {
            return this.selectedFieldFilterKey ?
                this.findFilterableFieldByKey(this.selectedFieldFilterKey) :
                null;
        },

        filterableFieldOptions() {
            const options = [];
            this.filterableFields.forEach((field) => {
                options.push({
                    id: field.display_key,
                    title: field.label
                });
            });

            return options;
        },

        isSearching() {
            return this.search && this.search !== '';
        },

        rowData() {
            if (!this.rows) {
                return [];
            }
            return this.hasPagination ? this.rows.data : this.rows;
        },

        hasPagination() {
            return this.rows && !Array.isArray(this.rows);
        },

        totalRows() {
            if (!this.rows) {
                return 0;
            }
            return this.hasPagination ? this.rows.total : this.rows.length;
        },

        calculatedFieldWidth() {
            return `${Math.max(10, 100 / this.renderFields.length)}%`;
        },

        selectedRows() {
            return this.rowData.filter((row, index) =>
                this.selectedRowIndexes.includes(index)
            );
        },

        isSelecting() {
            return this.selecting;
        },

        currentTemplate() {
            if (this.templates && this.templates[this.currentTemplateName]) {
                return this.templates[this.currentTemplateName];
            }
            return null;
        },

        templateOptions() {
            if (!this.templates) {
                return [];
            }

            const options = [];
            Object.keys(this.templates).forEach((key) => {
                options.push({
                    id: key,
                    title: key
                });
            });

            return options;
        },

        isFailed() {
            return this.rowError !== null;
        },

        isExportable() {
            return this.exportable;
        },

        isSearchable() {
            return this.searchable;
        },

        hasActions() {
            return this.actions.length > 0;
        },

        hasSetting() {
            return this.setting !== null;
        },

        isEmpty() {
            return (
                (!this.rowData || this.rowData.length === 0) && !this.rowError
            );
        },

        hasFooter() {
            return this.showPagination && this.hasPagination && this.rowData.length > 0;
        },

        isFilterable() {
            return !this.condensed;
        },
    },
    watch: {
        loading(v) {
            this.$emit('loading', v);
        },
        limit(v) {
            console.log('limit loading')
            this.loadData();
        },
        rows: {
            handler(newData) {
                newData = this.hasPagination ? this.rows.data : this.rows;
                if (newData) {
                    newData.forEach((row, index) => {
                        if (row.highlight) {
                            this.$nextTick(() => {
                                highlight(this.$refs[`row-${index}`]);
                                row.highlight = false;
                            });
                        }
                    });
                }
            },
            deep: true,
        },
        fields: {
            handler(newData) {
                // console.log(newData.length, this.fields.length);
                this.setupFields();
                // console.log(this.fields);
            },
            deep: true,
        },
    },
    created() {
        if (!this.url && !this.rawUrl) {
            console.error('SensesTable has not been provided a URL');
        }

        // @todo harry
        // if (!this.maintainerBinded) {
        //     this.importMaintainer();
        // }

        this.setupFields();
        if (this.hasSetting && !this.condensed) {
            console.log(this.load)
            this.loadUserSetting(this.load);
        } else if (this.load) {
            this.loadData();
        }

        if (this.event) {
            if (typeof this.event === 'string') {
                EventHub.on(this.event, () => {
                    this.loadData();
                });
            } else {
                this.event.forEach((event) => {
                    EventHub.on(event, () => {
                        this.loadData();
                    });
                });
            }
        }

        if (!this.load) {
            this.initialLoad = true;
        }
    },

    mounted() {
        EventHub.on(`${this.setting}-template-changed`, () =>
            this.loadUserSetting()
        );
    },

    unmounted() {
        EventHub.off(`${this.setting}-template-changed`, () =>
            this.loadUserSetting()
        );

        if (this.event) {
            if (typeof this.event === 'string') {
                EventHub.on(this.event, () => {
                    this.loadData();
                });
            } else {
                this.event.forEach((event) => {
                    EventHub.on(event, () => {
                        this.loadData();
                    });
                });
            }
        }
    },
    methods: {
        slotEmpty,
        isClient,

        altRowColour(rowColour){
            // if(rowColour.includes('-light')){
            //     return rowColour.replace('-light', '');
            // } else {
            //     return rowColour.concat('-dark');
            // }
        },

        rowID(row) {
            return this.table + '-row-' + row.id;
        },

        focusSearch(table) {
            setTimeout(function() {
                let searchInput = document.getElementById(table + '-search');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 200);
        },

        setUserLimit(limit) {
            if (limit <= 0) {
                limit = Math.floor((window.innerHeight - 368) / 43)
            }
            this.userLimit = limit;
            this.updateUserSetting(true, true);
        },

        onTableScroll({
            y
        }) {
            this.tableScroll = y;
        },
        capitalize,
        openColumnsMenu() {
            this.$asides.push('TableColumnsMenu', {
                table: this,
            });
        },

        openTableTemplatesMenu() {
            this.$asides.push('TableTemplatesForm', {
                table: this,
                setting: this.setting,
                currentTemplateName: this.currentTemplateName,
            });
        },

        async resetTableUserSetting() {
            // console.log('set', this.setting);
            const confirmed = await this.$dialogs.confirm("Reset Table Settings", "Are you sure you want to reset your settings for this table?");

            if (confirmed) {
                // console.log(this.setting);
                axios.delete(`/api/v2/users/${user().id}/user-settings/${this.setting}`).then(() => {
                    window.location.reload();
                });
            }
        },

        async importMaintainer() {
            if (this.maintainer) {
                // new myMaintainer(data);
                // console.log(this.maintainer);
                const Maintainer = (
                    await import(
                        `../../../Support/Maintainers/${this.maintainer}.js`
                    )
                ).default;
                this.tableMaintainer = new Maintainer(
                    this.rows,
                    this.maintainerId
                );
            }
            this.maintainerBinded = true;
        },

        unmounted() {
            this.tableMaintainer.destroy();
        },

        setFields(fields) {
            this.$emit('update:fields', fields);
        },

        setupFields() {
            Object.keys(this.fields).forEach((field) => {
                this.configureField(this.fields[field]);
            });

            this.fieldVisibility = this.defaultFieldVisibilities();
            this.$emit('update:fields', this.fields);
        },
        // Todo - add boolean option (returns hero icon of x or check)
        // Todo - add colour option returns a friendly colour component
        formatField(row, field) {
            if (field.format) {
                // console.log(field);
                if (field.format === 'datetime') {
                    return FormatDateTime(row[field.display_key]);
                } else if (field.format === 'date') {
                    return FormatDate(row[field.display_key]);
                } else if (field.format === 'money') {
                    return Currency(row[field.display_key]);
                } else if (field.format === 'percent') {
                    return row[field.display_key] + '%';
                } else if (field.format === 'noneable_money') {
                    return row[field.display_key] ? Currency(row[field.display_key]) : 'None';
                } else if (field.format === 'nullable_money') {
                    return row[field.display_key] ? Currency(row[field.display_key]) : null;
                } else if (field.format === 'minutes') {
                    return row[field.display_key] ? this.hoursAndMinutes(row[field.display_key]) : '00:00';
                }
            }
            return row[field.display_key || field.key];
        },

        hoursAndMinutes(totalMinutes){
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        },

        configureField(field) {
            if (!field.sort && field.sort !== false) {
                field.sort = true;
            }

            if (field.filter !== false) {
                if (!Object.prototype.hasOwnProperty.call(field, 'filter')) {
                    field.filter = {};
                }

                if (
                    !Object.prototype.hasOwnProperty.call(field.filter, 'key')
                ) {
                    field.filter.key = field.key;
                }
                if (
                    !Object.prototype.hasOwnProperty.call(field.filter, 'type')
                ) {
                    field.filter.type = 'text';
                }
                if (
                    !Object.prototype.hasOwnProperty.call(field.filter, 'value')
                ) {
                    field.filter.value = null;
                }

                if (
                    !Object.prototype.hasOwnProperty.call(
                        field.filter,
                        'sub_type'
                    )
                ) {
                    field.filter.sub_type = null;
                }
            }

            field.display_key = field.key.replaceAll('.', '__');

            const formatWidths = {
                // 'datetime': '170px'
            };

            if (!field.width) {
                field.width = formatWidths[field.format];
            }

            return field;
        },

        async loadData(baseUrl) {
            this.rowError = null;
            const url = this.rawUrl || this.queryUrl(baseUrl);
            this.loading = true;

            // console.log('loading');
            axios.create()(url).then(async (response) => {
                if (baseUrl) {
                    this.currentUrl = baseUrl;
                }

                this.rows = response.data;
                this.selectedRowIndexes = [];

                if (!this.maintainerBinded) {
                    this.importMaintainer();
                }

                await this.$nextTick(() => {
                    // console.log('loading stopped');
                    this.loading = false;
                    this.firstLoad = false;
                });

                this.initialLoad = true;
                this.$emit('updateTotalRows', this.rows.total);
            })
            .catch((error) => {
                this.loading = false;
                this.initialLoad = true;

                if (error.response && error.response.status === 403) {
                    this.rowError = `No permissions for ${this.table.replaceAll(/[_,-]/g, ' ')}`;
                } else {
                    this.rowError = `Failed to load ${this.table.replaceAll(/[_,-]/g, ' ')}`;
                }
            });
        },

        queryUrl(url) {
            url = url ?? this.currentUrl;
            let urlBuilder = new URL(url, window.location.href);
            const orderString = this.order === 'desc' ? '-' : '';

            if (
                this.sort &&
                this.hiddenFields.includes(this.sort) &&
                this.currentTemplate &&
                this.currentTemplate.fieldOrder?.length > 0
            ) {
                this.sort = this.currentTemplate.fieldOrder[0];
            } else if (
                this.sort &&
                this.hiddenFields.includes(this.sort)
            ) {
                this.sort = this.visibleFields[0].key;
            }

            urlBuilder.searchParams.append('sort', orderString + this.sort);
            urlBuilder = this.appendFields(urlBuilder);
            urlBuilder = this.appendFilters(urlBuilder);

            urlBuilder = this.appendAppends(urlBuilder);
            urlBuilder = this.appendIncludes(urlBuilder);

            urlBuilder.searchParams.append('limit', this.limit || this.userLimit || 25);

            return urlBuilder.href;
        },

        appendAppends(urlBuilder) {
            let appends = [];
            this.visibleFields.forEach((field) => {
                if (field.query == 'append') {
                    appends.push(field.key);
                }
            });

            if (appends.length > 0) {
                urlBuilder.searchParams.append('append', appends.join(','));
            }
            return urlBuilder;
        },

        appendIncludes(urlBuilder) {
            let includes = [];
            this.visibleFields.forEach((field) => {
                if (field.includes) {
                    includes = includes.concat(field.includes);
                }
            });

            if (includes.length > 0) {
                urlBuilder.searchParams.append('include', includes.join(','));
            }
            return urlBuilder;
        },

        appendFields(urlBuilder) {
            let fields = {};

            this.visibleFields.forEach((field) => {
                if (!(field.query == 'field' || field.query === undefined || field.query === null)) {
                    return;
                }

                fields = this.appendField(field.key, fields);

                if (field.fields) {
                    field.fields.forEach((subField) => {
                        fields = this.appendField(subField, fields);
                    });
                }
            });

            //right now this global required fields only applies to root table
            if (this.requiredFields) {
                this.requiredFields.forEach((fieldKey) => {
                    if (!fields[this.table].includes(fieldKey)) {
                        fields[this.table].push(fieldKey);
                    }
                });
            }

            // console.log(fields);

            //always include globally required fields
            // if(this.requiredFields) {
            //     this.requiredFields.forEach((fieldKey) => {
            //         if(!fields[fieldKey]) {
            //             const field  = this.fields.find((field) => field.key === fieldKey);
            //             if(field) {
            //                 fields[fieldKey] = field;
            //             }
            //         }
            //     });
            // }

            Object.keys(fields).forEach((relation) => {
                urlBuilder.searchParams.append(
                    `fields[${relation}]`,
                    fields[relation]
                );
            });

            return urlBuilder;
        },

        appendField(field, fields) {
            let parts = field.split('.');

            if (parts.length > 2) {
                parts = [parts.slice(0, -1).join('.'), parts[parts.length - 1]];
            }

            if (parts.length === 1) {
                parts.unshift(this.table);
            }

            if (!fields[parts[0]]) {
                fields[parts[0]] = [];
            }

            fields[parts[0]].push(parts[1]);

            return fields;
        },

        appendFilters(urlBuilder) {
            const filters = {};

            this.filteredFields.forEach((field) => {
                filters[field.filter.key] = field.filter.value;
            });

            if (this.isSearching) {
                filters.search = this.search;
            }

            Object.keys(filters).forEach((filterName) => {
                urlBuilder.searchParams.append(
                    `filter[${filterName}]`,
                    filters[filterName]
                );
            });

            return urlBuilder;
        },

        columnClass(column, index, rowIndex) {
            const classes = [
                'px-4 py-table whitespace-nowrap text-clip overflow-hidden break-all',
            ];

            if(column?.key) {
                classes.push(this.table + '-col-' + column.key.replace('.', '-'));
            }

            if (index === 0) {
                classes.push('text-black');
            } else {
                classes.push('text-black');
            }
            const firstColumnEnabled = this.isSelecting || (!this.isSelecting && this.rowExternalLinks);
            if (this.as.toLowerCase() == 'card' && !this.hasFooter && !slotEmpty(this.$slots['footer'])) {
                if (this.rowError) {
                    classes.push('rounded-b-lg');
                } else if ((index == 0) && (rowIndex === this.rowData.length - 1) && !firstColumnEnabled) {
                    classes.push('rounded-bl-lg');
                } else if ((index == this.renderFields.length - 1) && rowIndex === this.rowData.length - 1) {
                    classes.push('rounded-br-lg');
                }
            }

            return classes;
        },

        // headerColumnClass() {
        //     return 'px-4 py-2 text-left text-black';
        // },

        toggleFieldVisibility(fieldKey) {
            this.loading = true;
            const index = this.hiddenFields.findIndex((f) => f === fieldKey);

            if (index < 0) {
                if (this.renderFields.length === 1) {
                    this.loading = false;
                    return;
                }
                this.hiddenFields.push(fieldKey);

                if (fieldKey === this.sort) {
                    this.sortByField(this.renderFieldKeys[0] ?? null);
                }
            } else {
                this.hiddenFields.splice(index, 1);
                this.loadData(); // could avoid a reload, if a row already has it?
            }

            this.$nextTick(() => {
                this.loading = false; // work around for vue failing with dynamic slot visiblity
            });
        },

        isFieldVisible(fieldKey) {
            return !this.hiddenFields.includes(fieldKey);
        },

        sortByField(fieldKey, direction = null) {
            if (!this.isFieldVisible(fieldKey)) {
                return;
            }
            if (this.sort !== fieldKey) {
                this.order = direction ?? 'desc';
                this.sort = fieldKey;
            } else {
                this.order =
                    direction ?? (this.order === 'asc' ? 'desc' : 'asc');
            }

            this.loadData();
            this.updateUserSetting(true, false);
        },

        clearFilter(field) {
            field.filter.value = null;
            this.updateUserSetting(true, false);
            this.loadData();
        },

        clearFilters() {
            this.filteredFields.forEach(field => {
                field.filter.value = null;
            });
        },

        debounce,
        searchFilter: debounce(function searchFilterDebounced() {
            // Reset the pagination to page one on search, Otherwise returns an empty table
            var url = new URL(this.currentUrl, window.location.href);
            url.searchParams.set('page', 1);

            this.loadData(url.href);
        }, 250),

        // TODO Fix exports
        exportTable() {
            const queryUrl = this.queryUrl();

            const exportUrl = this.exportUrl ?? '/api/v2/exports/table';

            axios.post(exportUrl, { tableUrl: queryUrl, tableName: this.table }).then(() => {
                this.$notifications.push({
                    title: 'Export queued',
                    description: 'You will receive a notification when your export is ready.',
                    type: 'info',
                });
            })
            .catch(() => {
                this.$notifications.push({
                    title: 'Export failed',
                    description: 'We could not generate that export. Please try again later.',
                    type: 'danger',
                });
            });
        },

        showFilter(field) {
            // todo placeholder
        },

        toggleVisibility(event) {
            const visible = event.target.checked;
            if (visible) {
                this.hiddenFields = [];
            } else {
                this.fields.forEach((field, index) => {
                    if (index === 0) {
                        return; // skip first as we always need one column visible! (breaks sorting)
                    }
                    if (this.isFieldVisible(field.key)) {
                        this.hiddenFields.push(field.key);
                    }
                });
            }
        },

        applyFilter(field, {
            value,
            sub_type: subType,
            key
        }) {
            this.setFieldFilter(field, value, subType, key);

            this.loadData();
            this.updateUserSetting(true, false);
        },

        setFieldFilter(field, value, subType, key) {
            field.filter.value = value;
            field.filter.sub_type = subType;
            field.filter.key = key;
        },

        applyAction(action) {
            if (this.selectedRows.length > 0) {
                this.$emit('action-applied', action, this.selectedRows);
            }
        },

        toggleSelectedRows(checked) {
            if (checked) {
                this.rowData.forEach((row, index) => {
                    this.selectedRowIndexes.push(index);
                });
            } else {
                this.selectedRowIndexes = [];
            }
        },

        rowSelect(checked, row, index) {
            if (checked) {
                this.selectedRowIndexes.push(index);
            } else {
                const foundIndex = this.selectedRowIndexes.findIndex(
                    (rowIndex) => rowIndex === index
                );
                if (foundIndex > -1) {
                    this.selectedRowIndexes.splice(foundIndex, 1);
                }
            }
        },

        loadUserSetting(loadData = true) {
            this.loading = loadData;

            if (!this.hasSetting) {
                return;
            }

            axios.get(`/api/v2/users/${user().id}/user-settings/${this.setting}`).then((response) => {
                if (response.data.data.templates) {
                    this.templates = response.data.data.templates;
                }

                this.applyTemplate(response.data.data.currentTemplateName ?? this.currentTemplateName, loadData);
            })
            .catch(() => {
                this.loadData();
            });
        },

        updateUserSetting(setCurrentTemplate = false, reloadTable = false) {
            if (!this.hasSetting) {
                return;
            }

            this.loading = true;

            if (setCurrentTemplate) {
                this.currentTemplate.sort = clone(this.sort);
                this.currentTemplate.order = clone(this.order);
                this.currentTemplate.fieldVisibility = clone(this.fieldVisibility);
                this.currentTemplate.filters = clone(this.currentFilters);
                this.currentTemplate.fieldOrder = clone(this.fieldKeys);
                this.currentTemplate.search = clone(this.search);
                this.currentTemplate.limit = clone(this.userLimit);
            }

            axios
                .patch(
                    // eslint-disable-next-line no-undef
                    `/api/v2/users/${user().id}/user-settings/${this.setting}`, {
                        currentTemplateName: this.currentTemplateName,
                        templates: this.templates,
                    }
                )
                .then(() => {
                    if (reloadTable) {
                        this.loadData();
                    }
                    this.$notifications.push({
                        title: 'Table Settings saved',
                        description: '',
                        type: 'success',
                    });
                })
                .catch((e) => {
                    this.$notifications.push({
                        title: 'Table Settings failed to save',
                        description: '',
                        type: 'danger',
                    });
                    this.loading = false;
                });
        },

        findFilterableFieldByKey(key) {
            return this.filterableFields.find((field) => field.key === key);
        },

        applyTemplate(templateName, loadData = true) {
            if (!this.templates) {
                return;
            }

            if (!this.templates[templateName]) {
                return;
            }

            this.currentTemplateName = templateName;

            if (this.currentTemplate.sort !== null) {
                this.sort = this.currentTemplate.sort;
            }

            if (this.currentTemplate.limit !== null) {
                this.userLimit = this.currentTemplate.limit;
            }

            if (this.currentTemplate.order !== null) {
                this.order = this.currentTemplate.order;
            }

            if (this.currentTemplate.fieldVisibility) {
                this.fieldVisibility = this.currentTemplate.fieldVisibility;

                // This handles and sets null visibilities to false so that it doesn't cause issues. http://pm.senses.co.uk/projects/234?modal=Task-20578-234
                this.fields.forEach((field) => {
                    if (this.currentTemplate?.fieldVisibility && this.currentTemplate?.fieldVisibility.hasOwnProperty(field.key)) {
                        this.fieldVisibility[field.key] = this.currentTemplate?.fieldVisibility[field.key];
                    } else {
                        this.fieldVisibility[field.key] = false;
                    }
                });
            } else {
                this.currentTemplate.fieldVisibility = this.fieldVisibility;
                if (this.currentTemplate.hiddenFields) {
                    this.currentTemplate.hiddenFields.forEach(field => {
                        this.fieldVisibility[field] = false;
                    });
                }
            }

            if (this.currentTemplate.search !== null) {
                this.search = this.currentTemplate.search;
            }

            if (this.currentTemplate.fieldOrder !== null) {
                this.fields.sort((fieldA, fieldB) => {
                    const indexA = this.currentTemplate.fieldOrder.indexOf(
                        fieldA.key
                    );
                    const indexB = this.currentTemplate.fieldOrder.indexOf(
                        fieldB.key
                    );

                    if (indexA < indexB) {
                        return -1;
                    }
                    if (indexA > indexB) {
                        return 1;
                    }
                    return 0;
                });
            }

            this.clearFilters();
            if (this.currentTemplate.filters !== null) {
                this.currentTemplate.filters?.forEach((filter) => {
                    const field = this.findFilterableFieldByKey(filter.field_key);
                    if (field) {
                        this.setFieldFilter(
                            field,
                            filter.value,
                            filter.sub_type,
                            filter.key
                        );
                    }
                });
            }

            if (loadData) {
                this.loadData();
            }

            this.$emit('update:fields', this.fields);
        },

        saveNewTemplate(templateName, template) {
            this.templates[templateName] = {
                ...template,
            };

            this.applyTemplate(templateName);

            this.updateUserSetting(false);
        },

        addTemplate(templateName) {
            this.templates[templateName] = {
                sort: this.sort,
                order: this.order,
                fieldVisibility: this.fieldVisibility,
                filters: this.currentFilters,
                fieldOrder: this.fieldKeys,
                search: this.search,
            };

            this.currentTemplateName = templateName;

            this.updateUserSetting(false);
        },

        removeTemplate(templateName) {
            if (
                this.templates[templateName] &&
                this.currentTemplateName !== templateName
            ) {
                delete this.templates[templateName];
            }
        },

        defaultFieldVisibilities() {
            const fieldVisibility = {};

            this.fields.forEach((field) => {
                if (this.currentTemplate?.fieldVisibility) {
                    if (this.currentTemplate?.fieldVisibility.hasOwnProperty(field.key)) {
                        fieldVisibility[field.key] = this.currentTemplate?.fieldVisibility[field.key] ?? false;
                    } else {
                        fieldVisibility[field.key] = false;
                    }
                } else {
                    fieldVisibility[field.key] = !(field.hidden ?? false);
                }
            });

            return fieldVisibility;
        },

        resetTemplate(templateName) {
            this.templates[templateName] = {
                sort: this.sort,
                order: this.order,
                fieldVisibility: this.fieldVisibility,
                filters: [],
                fieldOrder: this.fieldKeys,
                search: '',
            };

            if (templateName === this.currentTemplateName) {
                this.applyTemplate(templateName);
                this.updateUserSetting(false, true);
            }
        },
    },
};
</script>
