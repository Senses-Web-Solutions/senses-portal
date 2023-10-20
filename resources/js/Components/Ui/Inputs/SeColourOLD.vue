<template>
    <SeInput
        :name="name"
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :error="error"
        :label="label"
    ></SeInput>
</template>
<script>
import SeInput from "../Inputs/SeInput.vue";
export default {
    components: {
        SeInput,
    },
    // emits: ['update:modelValue'],
    props: {
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
            required: true,
        },
    },
};
</script>
