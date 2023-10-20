<template>
    <div class="space-y-5">
        <div v-for="field in fields" :key="field.key">
            <SeValidation :error="error" :name="field.relationshipKey ?? field.key">
                <template v-if="field.component === 'template'">
                    <slot :name="field.name" :field="field"></slot>
                </template>
                <template v-else>
                    <component
                        :is="field.component"
                        v-model="modelValue[field.key]"
                        v-bind="field"
                        :initial-data="modelValue[field.dataKey] ?? null"
                        :editing="editing"
                        :model-id="modelId"
                        :error="error"
                        @permission-denied="$emit('permission-denied')" />

                    <p v-if="field.help" class="my-2 text-sm text-zinc-500">
                        {{ field.help }}
                    </p>
                </template>
            </SeValidation>
        </div>
    </div>
</template>

<script>
import SeInput from './Inputs/SeInput.vue';
import SeTextArea from './Inputs/SeTextArea.vue';
import SeRichTextArea from './Inputs/SeRichTextArea.vue';
import SeToggle from './Inputs/SeToggle.vue';
import SeColour from './Inputs/SeColour.vue';
import SeSelectBasic from './Inputs/SeSelectBasic.vue';
import SmallText from './Text/SmallText.vue';
import SeSelect from './Inputs/SeSelect.vue';
import SeSelectSearch from './Inputs/SeSelectSearch.vue';
import SeFileUpload from './Inputs/SeFileUpload.vue';
import SeFilePicker from './Inputs/SeFilePicker.vue';
import SeTimePicker from './Inputs/SeTimePicker.vue';
import SeDatePicker from './Inputs/SeDatePicker.vue';
import SeDateTimePicker from './Inputs/SeDateTimePicker.vue';
import SeDateRangePicker from './Inputs/SeDateRangePicker.vue';
import SeDateTimeRangePicker from './Inputs/SeDateTimeRangePicker.vue';
import SeValidation from './Inputs/SeValidation.vue';
import SeDateMeridiemPicker from './Inputs/SeDateMeridiemPicker.vue';
import SeMoneyInput from './Inputs/SeMoneyInput.vue';
import SeSignaturePad from './Inputs/SeSignaturePad.vue';

// todo styling for all selects
// todo error styling for all selects

export default {
    components: {
        SeValidation,
        SmallText,
        SeInput,
        SeTextArea,
        SeRichTextArea,
        SeToggle,
        SeColour,
        SeSelect,
        SeSelectBasic,
        SeSelectSearch,
        SeFileUpload,
        SeTimePicker,
        SeDatePicker,
        SeDateTimePicker,
        SeDateRangePicker,
        SeDateTimeRangePicker,
        SeFilePicker,
        SeDateMeridiemPicker,
        SeMoneyInput,
        SeSignaturePad,
    },
    props: {
        modelValue: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        error: {
            type: Object,
        },
        fieldTypes: {
            type: Object,
            default: () => {},
        },
    },
    emits: ['update:modelValue', 'update:model-value', 'update:fields', 'permission-denied'],
    computed: {
        editing() {
            return this.modelId !== null;
        },
        modelId() {
            return this.modelValue?.id;
        },
        availblefieldTypes() {
            return {
                ...this.fieldTypes,
                text: 'se-input',
                textarea: 'se-text-area',
                'rich-textarea': 'se-rich-text-area',
                toggle: 'se-toggle',
                email: 'se-input',
                number: 'se-input',
                colour: 'se-colour',
                'select-basic': 'se-select-basic',
                select: 'se-select',
                'select-search': 'se-select-search',
                file: 'se-file-picker',
                'file-picker': 'se-file-picker',
                'file': 'se-file-picker', //legacy, some forms are using type: 'file' and i probably haven't found them all
                'time-picker': 'se-time-picker',
                'date-picker': 'se-date-picker',
                'date-time-picker': 'se-date-time-picker',
                'date-range-picker': 'se-date-range-picker',
                'date-time-range-picker': 'se-date-time-range-picker',
                'date-meridiem-picker': 'se-date-meridiem-picker',
                money: 'se-money-input',
                signature: 'se-signature-pad',
            };
        },
    },
    watch: {
        fields: {
            deep: true,
            handler() {
                this.setupFields();
            }
        },
    },
    created() {
        this.setupFields();
    },
    methods: {
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        setupFields() {
            Object.keys(this.fields).forEach((field) => {
                this.configureField(this.fields[field]);
            });

            this.$emit('update:fields', this.fields);
        },

        configureField(field) {
            if (!field.name) {
                field.name = field.relationshipKey ?? field.key;
            }

            if (!field.id) {
                field.id = field.name;
            }

            if (field.label === undefined) {
                field.label = this.capitalizeFirstLetter(
                    field.name.replace('_id', '').replaceAll('_', ' ')
                );
            }

            field.component = this.determineComponent(field);

            return field;
        },

        determineComponent(field) {
            const component = 'unknown';
            if (!field.type) {
                console.error('Field Group field type is not set.', field);
                return component;
            }

            if (field.type === 'template') {
                return 'template';
            }

            if (this.availblefieldTypes[field.type]) {
                return this.availblefieldTypes[field.type];
            }

            if (component === 'unknown') {
                console.error('Field Group field type is not setup.', field);
            }

            return component;
        },
    },
};
</script>
