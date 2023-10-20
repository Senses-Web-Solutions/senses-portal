<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid"
        :error="error"
        :name="name"
        :help-text="helpText"
        inset-validation-icon
    >
        <Multiselect
            :id="id"
            :name="name"
            :options="options"
            :class="{ 'error': !isValid, 'rounded-md multiselect--rounded': rounded, 'allow_overflow': overflowResults }"
            class="flex-1 focus:ring-4 focus:ring-primary-200 focus:border-primary-500"
            :label="textField"
            :custom-label="textFieldFunc"
            :track-by="trackBy"
            :placeholder="placeholder"
            :multiple="multiple"
            :model-value="currentValue"
            :searchable="searchable"
            :disabled="disabled"
            :loading="loading"
            :allow-empty="allowEmpty"
            deselect-label=""
            select-label=""
            :close-on-select="closeOnSelect"
            @update:model-value="optionSelected">
        <template #tag="props">
            <slot name="tag" >
                <div class="bg-primary-500 text-white inline-block truncate rounded h-[22px] mr-2 mb-1">
                    <div class="flex items-center w-full h-full">
                        <div class="pl-2 pr-1">
                            {{ typeof props.option == "string" ? props.option : props.option[textField] }}
                        </div>
                        <div @mousedown.prevent="props.remove(props.option)" class="inline p-[3px] rounded hover:bg-primary-600 text-primary-700 hover:text-white">
                            <XIcon class="inline h-4 w-4"></XIcon>
                        </div>
                    </div>
                </div>
            </slot>
        </template>
        </Multiselect>
        <template #buttons v-if="clearable">
            <SecondaryButton size="xxs" class="py-0.25 px-1" @click="clearSelect">
                Clear
            </SecondaryButton>
        </template>
    </InputGroup>
</template>
<script>
import Multiselect from 'vue-multiselect';
import InputGroup from './InputGroup.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

import { XIcon } from "@heroicons/vue/outline";

export default {
    // todo Does not support listening to options currently, would have to either not build from options, or reinit select2 after vue has hit dom
    // todo optional props for clear/add all buttons
    components: {
        InputGroup,
        Multiselect,
        SecondaryButton,
        XIcon
    },
    props: {
        rounded: {
            type: Boolean,
            default: true
        },
        modelValue: {
            required: true,
        },
        label: {
            type: String,
            required: false,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        trackBy: {
            type: String,
            default: 'id',
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: 'Please select',
        },
        options: {
            type: Array,
        },
        error: {
            required: false,
            default: null,
        },
        textField: {
            type: String,
            default: 'title',
        },
        textFieldFunc: {
            type: Function,
            default: undefined
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        field: {
            type: String,
            default: null,
        },
        loading: {
            type: Boolean,
            deafult: false,
        },
        disabled: {
            type: Boolean,
            default: false
        },
        helpText: {
            type: [String, null],
            default: null,
        },
        overflowResults: { //adds a class to allow results to break out of width of original select, currently used on quote form code select!
            type: Boolean,
            default: false,
        },
        closeOnSelect: {
            type: Boolean,
            default: true,
        },
        allowEmpty: {
            type: Boolean,
            default: true,
        }
    },
    emits: ['update:modelValue'],
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
        currentValue() {
            if (this.field && this.options) {
                return this.options.filter(
                    (option) => option[this.field] === this.modelValue
                );
            }
            return this.modelValue;
        },
    },
    methods: {
        clearSelect() {
            this.$emit('update:modelValue', null);
        },
        optionSelected(option) {
            if (this.field && option) {
                this.$emit('update:modelValue', option[this.field]);
            } else {
                this.$emit('update:modelValue', option);
            }
        },
    },
};
</script>
