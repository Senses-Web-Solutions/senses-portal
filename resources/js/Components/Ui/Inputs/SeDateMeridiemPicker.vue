<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid(name,error)"
        :show-error-icon="false">
        <div class="flex items-center" :id="id" :class="{'flex space-x-2' : !hideTime}">
            <SeDatePicker
                :id="id + '_date'"
                :class="{ 'border-danger-500': !isValid }"
                :name="name + '_date'"
                :model-value="date"
                :error="error"
                :disabled="disabled"
                :valid="isValid(name, error)"
                :setDefault="setDefault"
                :fullWidth="hideTime ? true : false"
                @update:modelValue="calculateDateTime('date', $event)"></SeDatePicker>
            <SeSelect
                :id="id + '_time'"
                :class="{ 'border-danger-500': !isValid(name, error) }"
                :options="timeOptions"
                :name="name + '_time'"
                textField="title"
                field="id"
                :model-value="selectedTime"
                :error="error"
                :disabled="disabled"
                :valid="isValid(name, error)"
                @update:modelValue="calculateDateTime('time', $event)" />
            <XCircleIcon v-if="clearable" class="ml-2 w-5 h-5 text-zinc-500 cursor-pointer" @click="clear" />
        </div>
    </InputGroup>
</template>

<script>
import SeDatePicker from './SeDatePicker.vue';
import SeSelect from './SeSelect.vue';
import InputGroup from './InputGroup.vue';
import {
    format
} from 'date-fns';
import valid from '../../../Mixins/Valid';
import {
    XCircleIcon
} from '@heroicons/vue/outline';
export default {
    components: {
        SeDatePicker,
        SeSelect,
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
        },
        name: {
            type: String,
            required: true,
        },
        hideTime: {
            type: Boolean,
            default: false,
        },
        id: {
            type: String,
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
        dateFormat: {
            type: String,
            default: 'Y-m-d',
        },
        altFormat: {
            type: String,
            default: 'd/m/Y',
        },
        setDefault: {
            type: Boolean,
            default: true,
        },
        disabled: {
            type: Boolean,
            default: false
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        defaultTime: {
            type: String,
            default: 'am'
        },
        amTime: { //times am/pm start
            type: String,
            default: '09:00:00'
        },
        pmTime: {
            type: String,
            default: '13:00:00'
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            date: null,
            time: this.defaultTime == 'am' ? this.amTime : this.pmTime,
            selectedTime: this.defaultTime,
            timeOptions: [{
                    'title': 'AM',
                    'id': 'am'
                },
                {
                    'title': 'PM',
                    'id': 'pm'
                },
            ],
        };
    },
    created() {
        if (this.modelValue) {
            this.date = format(new Date(this.modelValue), 'yyyy-MM-dd');
            this.time = format(new Date(this.modelValue), 'HH:mm:ss');
        }
    },
    // computed: {
    //     isValid() {
    //         if (this.error && this.error.errors) {
    //             return !this.error.errors[this.name];
    //         }
    //         return true;
    //     },
    // },
    methods: {
        calculateDateTime(type, value) {
            if (value === null) {
                this.clear();
            }
            if (type === 'date') {
                this.date = value;
            } else if (type === 'time') {
                this.selectedTime = value;
                if (value == 'am') {
                    this.time = this.amTime;
                } else {
                    this.time = this.pmTime;
                }
            }

            if (this.date && this.time) {
                this.$emit('update:modelValue', `${this.date} ${this.time}`);
            }
        },
        clear() {
            this.date = null;
            this.time = null;
            this.$emit('update:modelValue', null);
        }
    },
    watch: {
        modelValue(newValue, oldValue) {
            if (newValue) {
                this.date = format(new Date(this.modelValue), 'yyyy-MM-dd');
                this.time = format(new Date(this.modelValue), 'HH:mm:ss');
            } else {
                this.date = null;
                this.time = null;
            }
        },
    },
};
</script>
