<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid(name, error)"
        :error="error"
        :class="fullWidth ? 'w-full' : 'w-2/3'"
        :name="name">
        <span class="flex items-center" :class="{ 'text-zinc-500 opacity-70': disabled }">
            <input :id="id" ref="dateField" type="text" :name="name" :disabled="disabled" :placeholder="placeholder" class="block w-full border-zinc-300 shadow-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-200" :class="{
                    'rounded-md': rounded,
                    'h-10': fullHeight
                }" />
            <XCircleIcon v-if="clearable" class="ml-2 h-5 w-5 cursor-pointer text-zinc-500" @click="clear" />
        </span>
    </InputGroup>
</template>

<script>
import flatpickr from 'flatpickr';
import {
    nextTick
} from 'vue';
import {
    format
} from 'date-fns';
import InputGroup from './InputGroup.vue';
import valid from '../../../Mixins/Valid';
import {
    XCircleIcon
} from '@heroicons/vue/outline';
import Format from '../../../Enums/Format';

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
        dateFormat: {
            type: String,
            default: 'Y-m-d',
        },
        altFormat: {
            type: String,
            default: 'd/m/Y',
        },
        fullWidth: {
            type: Boolean,
            default: false,
        },
        fullHeight: {
            type: Boolean,
            default: false,
        },
        calendar: {
            //if no calendar, it'll be a text input style with flatpickr validating data
            type: Boolean,
            default: true
        }
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
            if (this.flatpickr.altInput) {
                if (this.isValid(this.name, this.error)) {
                    this.flatpickr.altInput.classList.remove('border-danger-500');
                } else {
                    this.flatpickr.altInput.classList.add('border-danger-500');
                }
            }
        },
        error() {
            // ? flatpickr makes its own input for some reason.
            if (this.flatpickr.altInput) {
                if (this.isValid(this.name, this.error)) {
                    this.flatpickr.altInput.classList.remove('border-danger-500');
                } else {
                    this.flatpickr.altInput.classList.add('border-danger-500');
                }
            }
        },

        disabled() {
            // TODO: Sort out the styling of the date input
            if (this.flatpickr.altInput) {
                if (this.disabled) {
                    this.flatpickr.altInput.setAttribute('disabled', '');
                } else {
                    this.flatpickr.altInput.removeAttribute('disabled');
                }
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
            altFormat: this.altFormat,
            defaultDate: defaultDate,
            clickOpens: this.calendar,
            allowInput: !this.calendar,
            onChange: (selectedDates, dateStr) => {
                if (!dateStr) {
                    this.$emit(
                        'update:modelValue',
                        null
                    );
                } else {
                    this.$emit(
                        'update:modelValue',
                        this.multiple ? dateStr.split(', ') : dateStr
                    );
                }
            },
        });

        nextTick(() => {
            if (this.flatpickr.altInput) {
                if (this.isValid(this.name, this.error)) {
                    this.flatpickr.altInput.classList.remove('border-danger-500');
                } else {
                    this.flatpickr.altInput.classList.add('border-danger-500');
                }
            }
        });
    },
    methods: {
        format,
        clear() {
            this.$emit('update:modelValue', null);
        },
    },
    beforeUnmount() {
        this.flatpickr?.destroy();
    },
};
</script>
