<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid"
    >
    <div @click="focus" ref="inputWrapper" class="leading-5 cursor-text rounded-md border py-3 px-3 border-zinc-300 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-200 min-h-[120px]">
        <TextNode
            :modelValue="modelValue"
            class="block w-full"
            :class="{ 'border-red-500': !isValid }"
            @update:modelValue="$emit('update:modelValue', $event)"
        ></TextNode>
    </div>
    </InputGroup>
</template>
<script>
import { TextNode } from '@senses/builder';
import InputGroup from './InputGroup.vue';

export default {

    components: {
        InputGroup,
        TextNode
    },

    props: {
        label: {
            type: String,
            required: false,
            default: null,
        },
        fullHeight: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            required: true,
        },
        error: {
            type: Object,
            required: false,
            default: null,
        },
        placeholder: {
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

    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },

    methods: {
        focus() {
            this.$refs.inputWrapper.querySelector('.ProseMirror')?.focus();
        },
    },
};
</script>
<style>
.ProseMirror[contenteditable] [data-placeholder].is-empty:before {
    @apply text-zinc-400 hidden focus:border-purple-500 focus:ring-4 focus:ring-purple-200;
}
</style>
