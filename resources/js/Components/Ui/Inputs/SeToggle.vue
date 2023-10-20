<template>
    <InputGroup
        :label="label"
        :id="id"
        :is-valid="isValid"
        :layout="layout"
    >
        <Switch
            @update:model-value="$emit('update:modelValue', $event)"
            :model-value="modelValue"
            :class="[enabled ? 'bg-primary-600' : 'bg-zinc-200', 'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-primary-500']"
        >
            <span class="sr-only">Use setting</span>
            <span :class="[enabled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition ease-in-out duration-200']">
                <span
                    :class="[enabled ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
                    aria-hidden="true"
                >
                    <svg
                        class="w-3 h-3 text-zinc-400"
                        fill="none"
                        viewBox="0 0 12 12"
                    >
                        <path
                            d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </span>
                <span
                    :class="[enabled ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
                    aria-hidden="true"
                >
                    <svg
                        class="w-3 h-3 text-primary-500"
                        fill="currentColor"
                        viewBox="0 0 12 12"
                    >
                        <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                    </svg>
                </span>
            </span>
        </Switch>
    </InputGroup>
</template>

<script>
import { Switch } from '@headlessui/vue';
import InputGroup from './InputGroup.vue';

export default {
    components: {
        Switch,
        InputGroup,
    },
    props: {
        layout: {
            type: String,
            default: 'vertical',
        },
        label: {
            type: String,
            required: false,
        },
        modelValue: {
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: false,
        },
    },
    emits: ['update:modelValue'],
    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
        enabled() {
            return this.modelValue;
        },
    },
};
</script>
