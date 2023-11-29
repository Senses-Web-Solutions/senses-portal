<template>
    <div class="space-y-2">
        <div class="flex flex-col">
            <div :class="{ 'rounded-lg': rounded, 'border border-zinc-200': border, 'overflow-x-hidden overflow-y-auto max-h-[65vh]': restrictHeight }">
                <table v-if="data && data.length && columns && columns.length" class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th v-for="column in tableColumns" :key="column.key" scope="col">
                                <p class="px-6 py-2 text-left font-medium text-zinc-500">
                                    {{ column.title }}
                                </p>
                            </th>
                            <th v-if="!hideButtons" scope="col">
                                <p class="px-4 py-2 text-left font-medium text-zinc-500 truncate"></p>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        <tr v-for="(datum, index) in data" @click="executeRowClick(datum, index)" :key="index" class="divide-x divide-zinc-200" :class="rowClick && datum.hover !== false ? 'hover:cursor-pointer hover:bg-secondary-50' : null">
                            <template v-for="(column) in tableColumns" :key="column.key">
                                <td v-if="column.type === 'string'" class="px-6 py-3 text-zinc-700 max-w-md whitespace-normal">
                                    <SeInput :class="column.classes ?? null" :name="column.title" :id="column.field" v-if="column.editable" v-model="datum[column.field]" />
                                    <span v-else :class="column.classes ?? null">{{ column.field ? get(datum, column.field) : datum }}</span>
                                </td>
                                <td v-if="column.type === 'integer' || column.type === 'money'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md">
                                    <SeInput :class="column.classes ?? null" :id="column.field" :name="column.field" v-if="column.editable" v-model="datum[column.field]" :min="column.min ?? null"
                                        :max="column.max ?? null" type="number" />
                                    <span v-else :class="column.classes ?? null">
                                        {{ column.type === 'money' ? currency(get(datum, column.field)) : get(datum, column.field) }}
                                    </span>
                                </td>
                                <td v-if="column.type === 'colour'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md">
                                    <SeColour :class="column.classes ?? null" v-if="column.editable" v-model="datum[column.field]" />

                                    <Colour :class="column.classes ?? null" :colour="get(datum, column.field)" />
                                </td>
                                <td v-if="column.type === 'boolean'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md">
                                    <SeToggle :class="column.classes ?? null" v-if="(column.editable || datum.editable) && editable" :id="column.field" v-model="datum[column.field]"
                                        :name="column.title" />

                                    <BooleanText v-else :class="column.classes ?? null" :data="get(datum, column.field)" />
                                </td>
                                <td v-if="column.type === 'select'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md">
                                    <SeSelect :class="column.classes ?? null" v-if="(column.editable || datum.editable) && editable" :options="column.options" v-model="datum[column.field]"
                                        :name="column.title" field="id" :id="column.field" />
                                    <span v-else :class="column.classes ?? null">{{ get(datum, column.field) }}</span>
                                </td>
                                <td v-if="column.type === 'button'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md" :class="column.classes">
                                    <component v-if="Object.keys(datum).includes(column.field) && datum[column.field] !== null"
                                        @click="datum[column.field].function ? datum[column.field].function(datum) : column.function(datum)" :id="column.field" :name="column.title"
                                        :is="datum.buttonType ?? column.buttonType ?? 'SecondaryButton'" :class="datum.classes ?? null">
                                        {{ datum.title }}
                                    </component>
                                </td>
                                <td v-if="column.type === 'template'" class="px-6 py-3 whitespace-normal text-zinc-700 max-w-md" :class="column.classes">
                                    <slot :name="column.field" :index="index" :datum="datum" :column="column" :editable="editable" />
                                </td>
                            </template>
                            <td v-if="!hideButtons" class="px-4 py-3 whitespace-nowrap text-zinc-700 truncate">
                                <div class="flex justify-center">
                                    <ButtonGroup>
                                        <SecondaryButton v-if="orderable" size="xs" :disabled="!orderable || index === 0" @click="moveItemUp(datum, index)">
                                            <ArrowUpIcon class="h-5 w-5 my-auto" />
                                        </SecondaryButton>
                                        <SecondaryButton v-if="orderable" size="xs" :disabled="!orderable || index === Object.keys(data).length - 1" @click="moveItemDown(datum, index)">
                                            <ArrowDownIcon class="h-5 w-5 my-auto" />
                                        </SecondaryButton>
                                        <SecondaryButton size="xs" :disabled="deletableDisabled(datum)" @click="removeItem(datum, index)">
                                            <XIcon class="h-5 w-5 my-auto" />
                                        </SecondaryButton>
                                    </ButtonGroup>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="min-w-full">
                    <div v-if="$slots.empty">
                        <slot name="empty"></slot>
                    </div>
                    <EmptyState v-else />
                </div>
            </div>
        </div>
        <div class="flex justify-center" v-if="addable">
            <SecondaryButton size="xs" @click="addRow">
                <PlusIcon class="w-5 h-5" />
            </SecondaryButton>
        </div>
    </div>
</template>
<script>
import { get } from "lodash-es";
import { XIcon, ArrowUpIcon, ArrowDownIcon, PlusIcon } from '@heroicons/vue/outline';
import SecondaryAlert from "../Alerts/SecondaryAlert.vue";
import SecondaryButton from "../Buttons/SecondaryButton.vue";
import PrimaryButton from "../Buttons/PrimaryButton.vue";
import InfoButton from "../Buttons/InfoButton.vue";
import DangerButton from "../Buttons/DangerButton.vue";
import SuccessButton from "../Buttons/SuccessButton.vue";
import WarningButton from "../Buttons/WarningButton.vue";
import ButtonGroup from "../Buttons/ButtonGroup.vue";
import Colour from "../Colour.vue";
import SeInput from "../Inputs/SeInput.vue";
import SeColour from "../Inputs/SeColour.vue";
import SeToggle from "../Inputs/SeToggle.vue";
import EmptyState from "../EmptyState.vue";
import BooleanText from "../Text/BooleanText.vue";
import SeSelect from "../Inputs/SeSelect.vue";
import currency from "../../../Filters/Currency";

//For a button:
// {
//     title: 'Button',
//     type: 'button',
//     buttonType: 'PrimaryButton',
//     classes: 'min-w-44',
//     function: this.testFunction,
// }

//For a select:
// {
//     title: 'Select',
//     type: 'select',
//     classes: 'min-w-44',
//     field: 'select_relationship',
//     editable: true,
//     options: [{id: '1', title: 'Hi'},{id: '2', title: 'Hello'}]
// },

export default {
    components: {
        BooleanText,
        EmptyState,
        SeToggle,
        SeInput,
        SeColour,
        Colour,
        PrimaryButton,
        SecondaryButton,
        InfoButton,
        DangerButton,
        WarningButton,
        SuccessButton,
        SecondaryAlert,
        ButtonGroup,
        XIcon,
        PlusIcon,
        ArrowUpIcon,
        ArrowDownIcon,
        SeSelect
    },
    props: {
        data: { type: Array, default: () => ([]) }, //this should be using a writable proxy, however initial changes it wouldn't detect child changes and emit upwards
        columns: { type: Array },
        orderable: {
            default: true
        },
        editable: {
            default: true
        },
        deletable: {
            default: true
        },
        addable: {
            default: true
        },
        hideButtons: {
            default: false
        },
        border: {
            default: false
        },
        rounded: {
            default: true
        },
        restrictHeight: {
            default: true
        },
        rowClick: {
            type: Function,
            default: null
        }
    },
    emits: ['update:data', 'deleted'],

    computed: {
        fields() {
            var fields = [];
            if (this.columns) {
                this.columns.forEach(datum => {
                    fields.push(datum.field);
                })
            }
            return fields;
        },
        tableColumns() {
            if (this.columns) {
                return this.columns;
            }
            let columns = [];
            if (this.data && this.data.length) {
                columns = Object.keys(this.data[0]);
            }
            return columns;
        },
    },
    methods: {
        currency,
        deletableDisabled(datum) {
            // console.log(Object.keys(datum));
            if ((Object.keys(datum).includes('deletable') && datum.deletable === false) || this.deletable === false) {
                return true;
            }
            return false;
        },
        get,
        removeItem(item, index) {
            let array = this.data;
            array.splice(index, 1);
            array = array.filter((i) => i !== undefined);
            this.$emit('update:data', array);
            this.$emit('deleted', item);

        },

        moveItemUp(item, index) {
            const array = this.data;
            let newIndex = index - 1;
            while (index < 0) {
                index += array.length;
            }
            while (newIndex < 0) {
                newIndex += array.length;
            }
            if (newIndex >= array.length) {
                let k = newIndex - array.length + 1;
                while (k--) {
                    array.push(undefined);
                }
            }
            array.splice(newIndex, 0, array.splice(index, 1)[0]);
            this.$emit('update:data', array);
        },

        moveItemDown(item, index) {
            const array = this.data;
            let newIndex = index + 1;
            while (index < 0) {
                index += array.length;
            }
            while (newIndex < 0) {
                newIndex += array.length;
            }
            if (newIndex >= array.length) {
                let k = newIndex - array.length + 1;
                while (k--) {
                    array.push(undefined);
                }
            }
            array.splice(newIndex, 0, array.splice(index, 1)[0]);
            this.$emit('update:data', array);
        },

        addRow() {
            var proxyData = this.data;
            var emptyData = {};
            this.tableColumns.forEach((field, index) => {
                emptyData[field.field] = field.hasOwnProperty('default') ? field.default : null;
            });
            proxyData.push(emptyData);
            this.$emit('update:data', proxyData);
        },

        executeRowClick(datum, index) {
            if (this.rowClick) {
                this.rowClick(datum, index);
            }
        }
    },

}
</script>
