<template>
    <InputGroup
        :id="id"
        :label="label"
        :error="error"
        :name="name">
        <SeInput
            :id="id"
            :model-value="modelValue"
            :name="name"
            :placeholder="placeholder"
            type="number"
            @update:modelValue="confirm ? prepareForConfirm($event) : $emit('update:modelValue', $event)"
        >
            <template #icon>
                <p class="mb-0 text-lg font-bold">{{ currency }}</p>
            </template>
        </SeInput>

        <span 
            v-if="confirm && showConfirm" 
            class="absolute top-1/2 -translate-y-1/2 right-3 bg-primary text-white rounded h-5 w-5 flex justify-center items-center cursor-pointer" 
            @click="(e) => sendEmit(e)"
        >
            <CheckIcon class="h-4 w-4"/>
        </span>
        
    </InputGroup>
</template>

<script>
import { CheckIcon } from '@heroicons/vue/outline';
import SeInput from './SeInput.vue';
import InputGroup from './InputGroup.vue';

export default {
    components: {
        InputGroup,
        SeInput,
        CheckIcon,
    },
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
            required: false,
        },
        placeholder: {
            type: String,
            default: '0.00'
        },
        currency: {
            type: String,
            default: '£'
        },
        confirm: {
            type: Boolean,
            default: false
        },
    },
    emits: ['update:modelValue'],
    // setup() {
    //     const enabled = this.modelValue;
    //
    //     return {
    //         enabled,
    //     };
    // },
    data() {
        return {
            moneyValue: this.modelValue,
            showConfirm: false,
        }
    },
    computed: {
        // isValid() {
        //     if (this.error && this.error.errors) {
        //         return !this.error.errors[this.name];
        //     }
        //     return true;
        // },
        enabled() {
            return this.modelValue;
        },
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        },
        prepareForConfirm(e) {
            this.moneyValue = e;
            this.showConfirm = true;
        },
        sendEmit(e) {
            this.$emit('update:modelValue', this.moneyValue);
            this.showConfirm = false;
        }
    }
};
</script>
