<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid && !externalError"
        :required="required"
        :inset-validation-icon="type === 'number'"
        :flex-input="!!$slots.before"
    >
        <template v-if="$slots.icon" #icon>
            <slot name="icon"></slot>
        </template>

        <span v-if="$slots.before" class="inline-flex items-center rounded-l-md border border-r-0 border-zinc-300 bg-zinc-50 px-3 text-zinc-500 sm:text-sm">
            <slot name="before"></slot>
        </span>

        <input
            :id="id"
            v-bind="$attrs"
            ref="input"
            :type="type"
            :name="name"
            :value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :disabled="disabled"
            :max="max"
            :min="min"
            :step="step"
            class="block bg-white w-full min-h-10 rounded-r-md border-zinc-300 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
            :class="{
                'border-danger-500': !isValid || externalError,
                'rounded-l-0': $slots.icon || $slots.before,
                'rounded-l-md': !$slots.icon && !$slots.before,
                '!rounded-none': rounded === 'none',
            }"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </InputGroup>
</template>
<script>
import InputGroup from './InputGroup.vue';
// todo support prefix/suffix icons (prefix needed for search inputs)
// todo type:number still changes the number to a string, most forms need it as an integer
export default {
    components: {
        InputGroup,
    },
    props: {
        label: {
            type: String,
            required: false,
        },
        type: {
            default: 'text',
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
        placeholder: {
            // todo curently defined as props, could be v-bind="$attrs" to pass them all onto the input?
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        required: {
            type: Boolean,
            default: false,
        },
        externalError: {
            type: Boolean,
            default: false,
        },
        rounded: {
            type: String,
            default: null,
        },
        step: {
            type: Number,
            default: null,
        },
        autocomplete: {
            type: String,
            default: 'off',
        },
        max: Number,
        min: Number,
    },
    emits: ['update:modelValue'],
    computed: {
        isValid() {
            // todo harry move to inputGroup and slotProps?
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
