<template>
    <InputGroup :label="label" :id="id" :is-valid="isValid">
        <textarea
            :name="name"
            :id="id"
            class="block h-[120px] min-h-10 w-full rounded-md border-zinc-300 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-200"
            :placeholder="placeholder"
            :class="{ 'border-red-500': !isValid, 'h-[80vh]' : fullHeight }"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :rows="rows"
            ref="input"
        ></textarea>
    </InputGroup>
</template>
<script>
import InputGroup from './InputGroup.vue';

export default {
    components: {
        InputGroup,
    },

    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },
    props: {
        label: {
            type: String,
            required: false,
        },
        fullHeight: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
        placeholder: {
            //todo curently defined as props, could be v-bind="$attrs" to pass them all onto the input?
            type: String,
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
        rows: {
            type: Number,
            default: 5,
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
