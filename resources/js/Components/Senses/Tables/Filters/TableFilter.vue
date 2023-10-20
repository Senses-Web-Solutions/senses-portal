<template>
    <div class="space-y-2">
        <component
            :is="selectedSubType.component"
            v-if="selectedSubType && selectedSubType.component"
            :id="modelValue.key"
            :key="selectedSubType.key"
            :model-value="modelValue.value"
            :url="url"
            @update:modelValue="input('value', $event)"
        />
        <SeSelect
            v-if="subTypeOptions.length > 1"
            id="filterSubType"
            label="Type"
            field="id"
            name="filterSubType"
            :options="subTypeOptions"
            :model-value="subType"
            @update:modelValue="input('sub_type', $event)"
        ></SeSelect>
    </div>
</template>
<script>
import SeSelect from '../../../Ui/Inputs/SeSelect.vue';
import PrimaryButton from '../../../Ui/Buttons/PrimaryButton.vue';
import TextTableFilter from './TextTableFilter.vue';
import TextSuggestionsTableFilter from './TextSuggestionsTableFilter.vue';
import IntegerTableFilter from './IntegerTableFilter.vue';
import IntegerBetweenTableFilter from './IntegerBetweenTableFilter.vue';
import DateTimeTableFilter from './DateTimeTableFilter.vue';
import DateTimeBetweenTableFilter from './DateTimeBetweenTableFilter.vue';
import DateTableFilter from './DateTableFilter.vue';
import DateBetweenTableFilter from './DateBetweenTableFilter.vue';
import IssetBooleanTableFilter from './IssetBooleanTableFilter.vue';

export default {
    components: {
        SeSelect,
        PrimaryButton,
        TextTableFilter,
        IntegerTableFilter,
        IntegerBetweenTableFilter,
        DateTimeTableFilter,
        DateTimeBetweenTableFilter,
        DateTableFilter,
        DateBetweenTableFilter,
        IssetBooleanTableFilter,
        TextSuggestionsTableFilter,
    },
    props: {
        fieldKey: {
            type: String,
            required: true,
        },
        // filter: {
        //     type: Object,
        //     default: () => ({})
        // },
        modelValue: {
            type: Object,
            required: true
        },
        url:{
            type:String
        }
    },
    data() {
        return {
            subType:null
        };
    },
    emits: ['filtered', 'update:modelValue'],
    computed: {
        subTypes() {
            const subTypes = {
                integer: [
                    {
                        key: 'contains',
                        label: 'Contains',
                        component: 'IntegerTableFilter',
                    },
                    {
                        key: 'exact',
                        label: 'Exactly',
                        component: 'IntegerTableFilter',
                    },
                    {
                        key: 'between',
                        label: 'Between',
                        component: 'IntegerBetweenTableFilter',
                    },
                    {
                        key: 'greater_than',
                        label: 'Greater than',
                        component: 'IntegerTableFilter',
                    },
                    {
                        key: 'less_than',
                        label: 'Less than',
                        component: 'IntegerTableFilter',
                    },
                ],

                text: [
                    {
                        key: 'contains',
                        label: 'Contains',
                        component: 'TextSuggestionsTableFilter',
                    },
                    {
                        key: 'exact',
                        label: 'Exactly',
                        component: 'TextTableFilter',
                    },
                ],

                date_time: [
                    {
                        key: 'exact',
                        label: 'Exactly',
                        component: 'DateTimeTableFilter',
                    },
                    {
                        key: 'between',
                        label: 'Between',
                        component: 'DateTimeBetweenTableFilter',
                    },
                    {
                        key: 'greater_than',
                        label: 'Greater than',
                        component: 'DateTimeTableFilter',
                    },
                    {
                        key: 'less_than',
                        label: 'Less than',
                        component: 'DateTimeTableFilter',
                    },
                ],
                date: [
                    {
                        key: 'exact',
                        label: 'Exactly',
                        component: 'DateTableFilter',
                    },
                    {
                        key: 'between',
                        label: 'Between',
                        component: 'DateBetweenTableFilter',
                    },
                    {
                        key: 'greater_than',
                        label: 'Greater than',
                        component: 'DateTableFilter',
                    },
                    {
                        key: 'less_than',
                        label: 'Less than',
                        component: 'DateTableFilter',
                    },
                ],
                boolean: [
                    {
                        key: 'exact',
                        label: 'Exactly',
                        component: 'TextTableFilter',
                    },
                ],

                isset_boolean:[
                    {
                        key: 'isset_boolean',
                        label: 'Is',
                        component: 'IssetBooleanTableFilter',
                    },
                ],

                json_title:[
                    {
                        key: 'json_title_contains',
                        label: 'Contains',
                        component: 'TextTableFilter',
                    },
                    {
                        key: 'json_title_exact',
                        label: 'Exactly',
                        component: 'TextTableFilter',
                    },
                ],
            };

            const type = this.modelValue.type == 'datetime' ? 'date_time' :  this.modelValue.type;


            return subTypes[type] ?? {};
        },

        subTypeOptions() {
            let subTypeOptions = [];
            this.subTypes.forEach((subType) => {
                subTypeOptions.push({ id: subType.key, title: subType.label });
            });
            return subTypeOptions;
        },

        selectedSubType() {
            if (!this.subType) {
                return null;
            }

            if (!this.subTypes) {
                return null;
            }

            return this.subTypes.find((subType) => {
                return this.subType == subType.key;
            });
        },
    },
    watch: {
        filter(newVal) {
            this.subType = newVal.sub_type;
        },
        // modelValue(newVal, oldVal) {
        //     this.value = newVal;
        // },
        // modelValue() {
        //     const { modelValue } = this;
        //     modelValue.value = this.value;
        //     modelValue.subType = this.subType;
        //     this.$emit('update:modelValue', modelValue);
        // }
    },
    created() {
        if(this.modelValue.sub_type) {
            this.subType = this.modelValue.sub_type;
        }
        else if (this.subTypes.length > 0) {
            this.subType = this.subTypes[0].key;
        }
    },
    methods: {
        input(key, evt) {
            const { modelValue } = this;
            let { fieldKey } = this;

            modelValue[key] = evt;

            if(key === 'sub_type') {
                this.subType = evt;
            }

            // console.log(evt);
            if(this.subType !== 'contains') {
                fieldKey += `_${this.subType}`;
            }

            modelValue.key = fieldKey;

            this.$emit('update:modelValue', modelValue);
        },
        clearValue() {
            this.value = null;
        },
        applyFilter() {
            let key = this.fieldKey;
            if (this.subType !== 'contains') {
                key += `_${this.subType}`;
            }
            this.$emit('filtered', this.value, this.subType, key);
        },
    },
};
</script>
