<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid"
        :error="error"
        :name="name"
        inset-validation-icon
    >
        <Multiselect
            :id="id"
            :name="name"
            :options="options"
            :class="{ 'error': !isValid }"
            class="flex-1 rounded-md focus:ring-4 focus:ring-primary-200 focus:border-primary-500"
            :label="textField"
            :track-by="trackBy"
            :placeholder="placeholder"
            :multiple="true"
            :taggable="true"
            :model-value="currentValue"
            :disabled="disabled"
            deselect-label=""
            select-label=""
            @update:model-value="optionSelected"
            @tag="addTag"
        >
            <template #tag="props">
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
            </template>
        </Multiselect>
    </InputGroup>
</template>
<script>
import Multiselect from 'vue-multiselect';
import { kebabCase } from 'lodash-es';
import InputGroup from './InputGroup.vue';

import { XIcon } from "@heroicons/vue/outline";

export default {
    // todo Does not support listening to options currently, would have to either not build from options, or reinit select2 after vue has hit dom
    // todo optional props for clear/add all buttons
    components: {
        InputGroup,
        Multiselect,
        XIcon
    },
    props: {
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
        field: {
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false
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
        addTag(newTag) {
            const tag = {};
            tag[this.textField] = newTag;
            tag[this.trackBy] = kebabCase(newTag);
            let { modelValue } = this;
            if(!modelValue) {
                modelValue = [];
            }
            modelValue.push(tag);
            this.$emit('update:modelValue', modelValue);
        },
        optionSelected(option) {
            if (this.field) {
                this.$emit('update:modelValue', option[this.field]);
            } else {
                this.$emit('update:modelValue', option);
            }
        },
    },
};
</script>
