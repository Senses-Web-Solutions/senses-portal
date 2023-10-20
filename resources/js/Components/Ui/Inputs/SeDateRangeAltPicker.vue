<template>
    <InputGroup :label="label" :id="id" :is-valid="isValid">
        <SeDatePicker :full-width="fullWidth" :calendar="calendar" :class="{ 'border-danger-500': !isValid }" :name="name + '_start_date'" :id="id + '_start_date'" :model-value="endDate" class="mr-2" :default-time="defaultStartTime" @update:modelValue="calculateDate('startDate', $event)"></SeDatePicker>
        <p class="block font-medium leading-5 mt-2 text-zinc-700">to</p>
        <SeDatePicker :full-width="fullWidth" :calendar="calendar" :class="{ 'border-danger-500': !isValid }" :name="name + '_end_date'" :id="id + '_end_date'" :model-value="startDate" class="mr-2" :default-time="defaultEndTime" @update:modelValue="calculateDate('endDate', $event)"></SeDatePicker>
    </InputGroup>
</template>
<script>
import InputGroup from './InputGroup.vue';
import SeDatePicker from './SeDatePicker.vue';

export default {
    components: { InputGroup, SeDatePicker },
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
        calendar: {
            //if no calendar, it'll be a text input style with flatpickr validating data
            type:Boolean,
            default:true
        },
        fullWidth: {
            type:Boolean,
            default:false,
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            startDate:null,
            endDate:null
        }
    },
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },
    methods:{
        calculateDate(type, value) {
            if (value === null) {
                this.clear();
            }
            if(type === 'startDate') {
                this.startDate = value;
            }
            else if(type === 'endDate') {
                this.endDate = value;
            }

            if(this.startDate && this.endDate) {
                this.$emit('update:modelValue', {
                    start_date: this.startDate,
                    end_date: this.endDate
                });
            }
        },
        clear(){
            this.date = null;
            this.$emit('update:modelValue', null);
        }
    }

};
</script>
