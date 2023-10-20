<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid(name, error)"
        :error="error"
        :class="fullWidth ? 'w-full' : null"
        :name="name"
    >
        <span class="flex items-center">
            <input
                :id="id"
                ref="dateField"
                type="text"
                :name="name"
                :disabled="disabled"
                :placeholder="placeholder"
                class="block w-full border-zinc-300 shadow-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-200"
                :class="{ 'rounded-md': rounded }"
            />
            <XCircleIcon
                v-if="clearable"
                class="ml-2 h-5 w-5 cursor-pointer text-zinc-500"
                @click="clear"
            />
        </span>
    </InputGroup>
</template>
<script>
import flatpickr from 'flatpickr';
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect';
import { nextTick } from 'vue';
import { format } from 'date-fns';
import InputGroup from './InputGroup.vue';
import valid from '../../../Mixins/Valid';
import Format from '../../../Enums/Format';
import { XCircleIcon } from '@heroicons/vue/outline';

export default {
    components: {
        InputGroup,
        XCircleIcon,
    },
    mixins: [valid],
    props: {
        modelValue: {
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: '',
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
            default: '',
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        rounded: {
            type: Boolean,
            default: true,
        },
        error: {
            type: [Object, null],
            required: false,
            default: null,
        },
        setDefault: {
            type: Boolean,
            default: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        fullWidth: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:modelValue'],
    data() {
        return {
            flatpickr: null,
        };
    },
    // computed: {
    //     isValid() {
    //         if (this.error && this.error.errors) {
    //             return !this.error.errors[this.name];
    //         }
    //         return true;
    //     },
    // },
    watch: {
        modelValue(newValue) {
            // eslint-disable-next-line no-underscore-dangle
            this.$refs.dateField._flatpickr.setDate(newValue, false, 'Y-m-d');
        },
        valid() {
            // ? flatpickr makes its own input for some reason.
            if (this.isValid(this.name, this.error)) {
                this.flatpickr.altInput.classList.remove('border-danger-500');
            } else {
                this.flatpickr.altInput.classList.add('border-danger-500');
            }
        },
        error() {
            // ? flatpickr makes its own input for some reason.
            if (this.isValid(this.name, this.error)) {
                this.flatpickr.altInput.classList.remove('border-danger-500');
            } else {
                this.flatpickr.altInput.classList.add('border-danger-500');
            }
        },

        disabled() {
            // TODO: Sort out the styling of the date input
            if (this.disabled) {
                this.flatpickr.altInput.setAttribute('disabled', '');
            } else {
                this.flatpickr.altInput.removeAttribute('disabled');
            }
        },
    },
    mounted() {
        let defaultDate = this.modelValue;
        if (defaultDate === null && this.setDefault) {
            defaultDate = format(new Date(), Format.DATE);
            this.$emit('update:modelValue', defaultDate);
        }
        this.flatpickr = flatpickr(this.$refs.dateField, {
            mode: this.multiple ? 'multiple' : 'single',
            dateFormat: this.dateFormat,
            altInput: true,
            plugins: [
                new monthSelectPlugin({
                    dateFormat: this.dateFormat,
                    altFormat: 'Y',
                }),
            ],
            altFormat: 'Y',
            defaultDate: defaultDate,
            onChange: (selectedDates, dateStr) => {
                this.$emit(
                    'update:modelValue',
                    this.multiple ? dateStr.split(', ') : dateStr
                );
            },
        });

        nextTick(() => {
            if (this.isValid(this.name, this.error)) {
                this.flatpickr.altInput.classList.remove('border-danger-500');
            } else {
                this.flatpickr.altInput.classList.add('border-danger-500');
            }
        });
    },
    methods: {
        clear() {
            this.$emit('update:modelValue', null);
        },
    },
    beforeUnmount() {
        this.flatpickr?.destroy();
    },
};
</script>
