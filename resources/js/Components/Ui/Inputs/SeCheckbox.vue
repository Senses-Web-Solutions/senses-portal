<template>
    <InputGroup
        class="flex items-center justify-center"
        :label="label"
        :is-valid="isValid"
        :id="id"
    >
        <input
            :id="id"
            ref="input"
            v-model="proxyValue"
            type="checkbox"
            :name="name"
            :disabled="disabled"
            class="border border-zinc-300 rounded text-primary-500 focus:ring-primary-200"
            :class="{ 'border-danger-500': !isValid }"
        >
    </InputGroup>
</template>
<script>
import InputGroup from './InputGroup.vue';

export default {
    components: {
        InputGroup,
    },

    props: {
        modelValue: {
            type: Boolean,
            default: false,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: ''
        },
        error: {
            type: Object,
            required: false,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },

    emits: ['update:modelValue'],

    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },

        proxyValue: {
            get() {
                return this.modelValue;
            },

            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
