<template>
    <SeInput
        :id="`filter[${id}]`"
        :name="`filter[${id}]`"
        :model-value="modelValue"
        label="Filter"
        placeholder="Search"
        @update:modelValue="$emit('update:modelValue', $event)"
    ></SeInput>
</template>
<script>
import SeInput from '../../../Ui/Inputs/SeInput.vue';

export default {
    components: { SeInput },
    props: {
        id: {
            type: String,
        },
        modelValue: {
            type: String,
        },
    },
    emits: ['update:modelValue'],
};
</script>