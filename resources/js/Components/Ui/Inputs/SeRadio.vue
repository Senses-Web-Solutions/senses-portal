<template>
    <div class="my-2 flex items-center">
        <input
            :id="field + label"
            ref="input"
            type="radio"
            :name="field"
            class="border text-primary-500"
            :class="{ 'border-red-500': !isValid }"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <SeLabel
            :for="field + label"
            class="ml-2 tabular-nums"
            >{{ label }}</SeLabel
        >
    </div>
    <error
        v-if="!isValid"
        :message="error.errors[field][0]"
    ></error>
</template>
<script>
import SeLabel from './SeLabel.vue';
import Valid from '../../../Mixins/Valid';

export default {
    components: {
        SeLabel,
        Error,
    },
    mixins: [Valid],
    props: {
        modelValue: {
            required: true,
        },
        field: {
            required: true,
        },
        label: {
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
    },
    emits: ['update:modelValue'],
    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
