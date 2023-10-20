<template>
    <InputGroup
        :id="id"
        class="w-full"
        :label="label"
        :is-valid="isValid(name,error)"
    >
        <div class="flex items-center w-full" :class="{
                'flex space-x-2' : !hideTime,
            }">
            <SeDatePicker
                :id="id + '_date'"
                :class="{ 'border-danger-500': !isValid }"
                :name="name + '_date'"
                :model-value="date"
                :error="error"
                :disabled="disabled"
                :valid="isValid(name, error)"
                :setDefault="setDefault"
                :fullWidth="hideTime || fullWidth ? true : false"
                :calendar="calendar"
                :fullHeight="true"
                @update:modelValue="calculateDateTime('date', $event)"
            ></SeDatePicker>
            <SeTimePicker
                v-if="!hideTime"
                :id="id + '_time'"
                :class="{ 'border-danger-500': !isValid(name, error) }"
                :name="name + '_time'"
                :model-value="time"
                :error="error"
                :disabled="disabled"
                :valid="isValid(name, error)"
                :min-hour="minHour"
                :max-hour="maxHour"
                @update:modelValue="calculateDateTime('time', $event)"
            ></SeTimePicker>
            <XCircleIcon v-if="clearable" class="ml-2 w-5 h-5 text-zinc-500 cursor-pointer" @click="clear"/>
        </div>
    </InputGroup>
</template>
<script>
import SeDatePicker from './SeDatePicker.vue';
import SeTimePicker from './SeTimePicker.vue';
import InputGroup from './InputGroup.vue';
import { format } from 'date-fns';
import valid from '../../../Mixins/Valid';
import {XCircleIcon} from '@heroicons/vue/outline';
export default {
    components: {
        SeDatePicker,
        SeTimePicker,
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
        fullWidth: {
            type: Boolean,
            default: false,
        },
        id: {
            type: String,
            required: true,
        },
        defaultTime: {
            type: String,
            default: '00:00:00',
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
        minHour:{
            type:Number,
            default: 0,
        },
        maxHour:{
            type:Number,
            default: 24,
        },
        calendar: {
            type:Boolean,
            default:true,
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            date: null,
            time: this.defaultTime,
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
                this.time = value;
            }

            if (this.date && this.time) {
                this.$emit('update:modelValue', `${this.date} ${this.time}`);
            }
        },
        clear(){
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
        date() {
            if (this.date && !this.time) {
                this.time = '00:00:00';
            }
        }
    },
};
</script>
