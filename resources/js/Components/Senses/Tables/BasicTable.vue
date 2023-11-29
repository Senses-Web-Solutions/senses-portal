<template>
    <div class="relative">
        <CollapseTransition>
            <component :is="as" :flush="true">
                <template v-if="as.toLowerCase() == 'card'" #title>
                    {{ title }}
                </template>
                <template v-if="!condensed && as.toLowerCase() === 'card'" #actions >
                    <PlusButton v-if="plusButton" :disabled="plusButton.disabled" :form="plusButton.form" :data="plusButton.data">
                        <PlusIcon class="mx-auto h-[20px] w-4" />
                    </PlusButton>
                </template>

                <IndeterminateLoadingBar v-if="loading" class="absolute left-0 right-0 z-10"/>
                <div class="relative flex flex-col">
                    <div v-if="!loading && isEmpty">
                        <slot name="table-empty">
                            <EmptyState>This table has no data.</EmptyState>
                        </slot>
                    </div>
                    <div v-else class="-mt-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="min-w-full overflow-hidden pt-2 align-middle sm:px-6 lg:px-8">
                            <div class="overflow-x-auto">
                                <table :class="{'border': bordered}" class="min-w-full divide-y divide-zinc-200">
                                    <thead class="divide-y divide-zinc-200">
                                        <template v-if="doubleColumns">
                                            <tr class="divide-x divide-zinc-200">
                                                <th v-for="(header, index) in columns" :key="index" :name="index + '_header'" class="whitespace-nowrap border-r px-4 py-table text-zinc-500" :class="header.columns ? 'text-center' : 'text-left align-bottom'" :colspan="header.columns ? header.columns.length : 1" :rowspan="header.columns ? 1 : 2" :width="header.width">
                                                    {{ header.title }}
                                                </th>
                                            </tr>
                                            <tr class="divide-x divide-zinc-200">
                                                <template v-for="(header, index) in columns">
                                                    <th v-if="header.columns" v-for="(subHeader, index) in header.columns" :key="index" :name="index + '_sub_header'" class="whitespace-nowrap px-4 py-table text-zinc-500">
                                                        {{ subHeader.title }}
                                                    </th>
                                                </template>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr class="divide-x divide-zinc-200">
                                                <th v-for="(header, index) in columns" :key="index" :name="index + '_header'" class="whitespace-nowrap px-4 py-table text-left text-zinc-500 capitalize" :width="header.width">
                                                    {{ header.title }}
                                                </th>
                                            </tr>
                                        </template>
                                    </thead>

                                    <tbody :class="{ border: bordered }" class="divide-y divide-zinc-200">
                                        <tr v-for="(row, rowIndex) in rows" :ref="'row-' + rowIndex" :key="`row${rowIndex}`" :class="rowClasses(row, rowIndex)" class="divide-x divide-zinc-200" :style="{'--tw-transition-delay': `${rowIndex * 25}ms`}" @click="$emit('rowClick', row)">
                                            <template v-for="(data, key) in destructuredColumns" :key="key">
                                                <td :class="columnClass(row[data.id], data.id, rowIndex)">
                                                    <slot v-if="isTemplate(row[data.id], data.id, rowIndex)" :col="row[data.id]" :row="row" :index="data.id" :name="data.id"></slot>
                                                    <slot v-else-if="data.format == 'boolean'" :col="row[data.id]" :row="row" :index="data.id" :name="data.id">
                                                        {{ row[data.id] ? '&#10003;' : '&#10005;'}}
                                                    </slot>
                                                    <slot v-else-if="data.format == 'badge'" :col="row[data.id]" :row="row" :index="data.id" :name="data.id">
                                                        <Badge :background-colour="row[data.id]?.colour" :text-colour="row[data.id]?.text_colour">
                                                            {{ row[data.id]?.title}}
                                                        </Badge>
                                                    </slot>
                                                    <slot v-else-if="data.format == 'tag' && row[data.id]" :col="row[data.id]" :row="row" :index="data.id" :name="data.id">
                                                        <Tag :colour="row[data.id]?.colour ?? null">
                                                            {{ row[data.id]?.full_name ?? row[data.id]?.title }}
                                                        </Tag>
                                                    </slot>
                                                    <span v-else>
                                                        {{ formatField(row, data.id)}}
                                                    </span>
                                                </td>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </component>
        </CollapseTransition>
    </div>
</template>
<script>
import { capitalize } from 'lodash-es';
import { PlusIcon } from '@heroicons/vue/outline';
import { Icon } from '@iconify/vue';
import PlusButton from '../../Ui/Buttons/PlusButton.vue';
import Badge from '../../Ui/Badges/Badge.vue';
import Tag from '../../Ui/Tags/Tag.vue';
import Card from '../../Ui/Cards/Card.vue';

import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import CollapseTransition from '../../Ui/Transitions/CollapseTransition.vue';
import FormatDateTime from '../../../Filters/FormatDateTime';
import FormatDate from '../../../Filters/FormatDate';
import Currency from '../../../Filters/Currency';
import EmptyState from '../../Ui/EmptyState.vue';

export default {
    components: {
        EmptyState,
        IndeterminateLoadingBar,
        CollapseTransition,
        PlusButton,
        Tag,
        Card,
        PlusIcon,
        Badge,
        Icon,
    },
    props: {
        bordered: {
            type: Boolean,
            default: false,
        },
        selectable: {
            type: Boolean,
            default: true,
        },
        title: {
            type: String,
            default: null,
        },
        rows: { type: Array, default: () => [] },
        columns: { type: Array },
        condensed: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        as: {
            type: String,
            default: 'Card',
        },
        plusButton: {
            type: Object,
            default: null,
        },
    },
    emits: ['rowClick'],
    data() {
        return {
            //columns and rows props should be in these formats:
            columnsTemplate: [
                { id: 1, title: 'ABC' },
                { id: 2, title: 'Date', format: 'datetime' },
            ],
            rowsTemplate: [
                { 1: 'hello there', 2: '12-12-2022' },
                { 1: 'hi', 2: '10-12-1999' },
            ],
        };
    },
    computed: {
        doubleColumns() {
            return this.columns.filter((column) => column.columns).length > 0;
        },
        totalRows() {
            if (!this.rows) {
                return 0;
            }
            return this.rows.length;
        },

        calculatedFieldWidth() {
            return `${Math.max(10, 100 / this.headers.length)}%`;
        },

        isEmpty() {
            return !this.rows || this.rows.length === 0;
        },

        destructuredColumns() {
            var cols = [];
            this.columns.forEach((column) => {
                if (column.columns) {
                    cols = cols.concat(column.columns);
                } else {
                    cols.push(column);
                }
            });
            return cols;
        },
        // formattedRows() { //didn't even need this but keeping it just in case i screwed up
        //     var formattedRows = this.rows;
        //     var columnKeys = Object.values(this.destructuredColumns).map(val => val.id);
        //     Object.values(formattedRows).forEach((row) => {
        //         Object.keys(row).forEach((key) => columnKeys.includes(key) || delete row[key]);
        //     });
        //     return formattedRows;
        // },
    },
    methods: {
        capitalize,
        Currency,
        rowClasses(row, rowIndex){
            var classes = [];
            if(rowIndex % 2 === 0){
                classes.push('bg-white');
            } else {
                classes.push('bg-zinc-50');
            }

            if(this.selectable){
                classes.push('cursor-pointer hover:bg-zinc-100');
            }
            if(row.classes){
                classes.push(row.classes);
            }
            return classes.join(' ');
        },
        formatField(row, index) {
            var columnIndex = this.destructuredColumns.findIndex(
                (c) => c.id === index
            );
            var columnFormat = 'string';

            if (
                columnIndex != -1 &&
                this.destructuredColumns[columnIndex].format
            ) {
                columnFormat = this.destructuredColumns[columnIndex].format;
            }

            if (columnFormat == 'datetime') {
                if(row[index] && /\d/.test(row[index])){
                    return FormatDateTime(row[index]);
                }
            } else if (columnFormat == 'date') {
                if(row[index] && /\d/.test(row[index])){
                    return FormatDate(row[index]);
                }
            } else if (columnFormat == 'time') {
                if(row[index] && /\d/.test(row[index])){
                    return FormatDateTime(row[index], 'hh:mmaaa');
                }
            } else if (columnFormat == 'money') {
                return Currency(row[index], 0);
            } else if (columnFormat == 'noneable_money') {
                return row[index] ? Currency(row[index]) : 'None';
            } else if (columnFormat == 'negative_money') {
                return '- ' + Currency(row[index]);
            } else if (columnFormat == 'percentage') {
                return row[index] ? parseFloat(row[index]).toFixed(2) + '%' : '0%';
            } else if (columnFormat == 'minutes') {
                if(!row[index]){
                    return '00:00';
                }
                if (row[index] < 0) {
                    var data = row[index] * -1;
                    return '-' + (Math.trunc(data / 60)).toString().padStart(2, '0') + ':' + (data % 60).toString().padStart(2, '0');
                } else {
                    return (Math.trunc(row[index] / 60)).toString().padStart(2, '0') + ':' + (row[index] % 60).toString().padStart(2, '0');
                }
            }

            return row[index];
        },

        isTemplate(rowData, columnKey, rowIndex) {
            var columnIndex = this.columns.findIndex((c) => c.id === columnKey);
            return (
                this.columns[columnIndex] &&
                this.columns[columnIndex].format &&
                this.columns[columnIndex].format == 'template'
            );
        },

        columnClass(rowData, columnKey, rowIndex) {
            var columnIndex = this.columns.findIndex((c) => c.id === columnKey);
            var columnData = this.columns[columnIndex];
            const classes = [
                'px-4 py-table whitespace-nowrap text-clip overflow-hidden break-all',
            ];
            if (
                this.as == 'card' &&
                columnIndex == 0 &&
                rowIndex === this.rows.length - 1
            ) {
                classes.push('rounded-bl-lg');
            } else if (
                this.as == 'card' &&
                columnIndex - 1 == this.rows.length
            ) {
                classes.push('rounded-br-lg');
            }

            if (
                this.rows[rowIndex].highlightRow &&
                this.rows[rowIndex].highlightRow == true
            ) {
                classes.push('font-bold bg-primary-50');
            }

            if(columnData && columnData.classes){
                classes.push(columnData.classes);
            }

            return classes;
        },
    },
};
</script>
