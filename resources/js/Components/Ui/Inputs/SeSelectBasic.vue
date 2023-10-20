<template>
<InputGroup
    :label="label"
    :id="id"
    inset-validation-icon>
    <select
        :multiple="multiple"
        :name="name"
        :disabled="disabled"
        class="block w-full h-10 border border-zinc-300 rounded-md shadow-sm form-input [font-size:0.9rem] focus:ring-4 focus:ring-primary-200 focus:border-primary-500"
        :class="{ 'border-danger-500': !isValid }"
        :id="id"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)">
        <option
            value=""
            v-if="placeholder"
            disabled
            hidden
            selected>{{ placeholder }}</option>
        <option
            :value="option.id"
            v-for="option in options"
            :key="option.id">{{ option[textField] }}</option>
    </select>
</InputGroup>
</template>

<script>
import InputGroup from "./InputGroup.vue";
//todo change to headlessui/tailwindui version, not implemented because tailwind version only just been updated and it was "odd", not entirely sure we need this field hence the default being the "select" field
export default {
    components: {
        InputGroup
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
        placeholder: {
            type: String,
            default: "Please select",
        },
        options: {
            type: Object,
        },
        textField: {
            type: String,
            default: "title",
        },
        error: {
            type: Object,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false
        },
        isValid: {
            type: Boolean,
            default: true
        },
    },
    computed: {
        // isValid() {
        //     if (this.error && this.error.errors) {
        //         return !this.error.errors[this.name];
        //     }
        //     return true;
        // },
    },
    emits: ["update:modelValue"],
};
</script>
