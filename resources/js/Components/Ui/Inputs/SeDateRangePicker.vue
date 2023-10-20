<template>
    <InputGroup :id="id" :label="label" :is-valid="isValid">
        <input
            :id="id"
            type="text"
            :name="name"
            :placeholder="placeholder"
            ref="dateField"
            :class="{ 'border-danger-500': !isValid }"
            class="block w-full border-zinc-300 rounded-md shadow-sm form-input sm:sm:leading-5"
        />
    </InputGroup>
</template>
<script>
import flatpickr from 'flatpickr';
import InputGroup from './InputGroup.vue';
import { format } from 'date-fns';

export default {
    components: { InputGroup },
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
        startDate: {
            type: String,
            default: null,
        },
        endDate: {
            type: String,
            default: null,
        },
        error: {
            type: Object,
            default: null,
        },
    },
    emits: ['update:modelValue'],
    data() {
        return {
            flatpickr: null,
            datesSet: false,
        };
    },
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },
    watch: {
        modelValue(newVal) {
            const dates = [];
            console.log(newVal);

            if (newVal && newVal.start_date) {
                dates.push(newVal.start_date);
            }

            if (newVal && newVal.end_date) {
                dates.push(newVal.end_date);
            }

            this.flatpickr.setDate(dates);
        },
    },
    mounted() {
        this.flatpickr = flatpickr(this.$refs.dateField, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            defaultDate: this.modelValue ? [this.modelValue.start_date, this.modelValue.end_date] : null,

            onChange: (selectedDates, dateStr) => {
                let endDate = null;

                if (selectedDates.length > 1) {
                    endDate = format(selectedDates[1], 'yyyy-MM-dd');
                }

                this.$emit('update:modelValue', {
                    start_date: format(selectedDates[0], 'yyyy-MM-dd'),
                    end_date: endDate,
                });
            },

            onClose: () => {
                if (this.flatpickr.selectedDates.length == 1) {
                    this.$emit('update:modelValue', {
                        start_date: format(this.flatpickr.selectedDates[0], 'yyyy-MM-dd'),
                        end_date: format(this.flatpickr.selectedDates[0], 'yyyy-MM-dd'),
                    });
                }
            }
        });
    },

    beforeUnmount() {
        this.flatpickr.destroy();
    },
};
</script>
