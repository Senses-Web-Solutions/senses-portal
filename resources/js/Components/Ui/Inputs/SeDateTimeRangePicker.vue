<template>
    <InputGroup :label="label" :id="id" :is-valid="isValid">
        <SeDateTimePicker :calendar="calendar" :class="{ 'border-danger-500': !isValid }" :name="name + '_start_date'" :id="id + '_start_date'" :model-value="endDate" class="mr-2" :default-time="defaultStartTime" @update:modelValue="calculateDateTime('startDate', $event)"></SeDateTimePicker>
        <p class="block font-medium leading-5 mt-2 text-zinc-700">to</p>
        <SeDateTimePicker :calendar="calendar" :class="{ 'border-danger-500': !isValid }" :name="name + '_end_date'" :id="id + '_end_date'" :model-value="startDate" class="mr-2" :default-time="defaultEndTime" @update:modelValue="calculateDateTime('endDate', $event)"></SeDateTimePicker>
    </InputGroup>
</template>
<script>
import SeDateTimePicker from './SeDateTimePicker.vue';
import InputGroup from './InputGroup.vue';
export default {
    components:{
        InputGroup,
        SeDateTimePicker
    },
    props:{
        modelValue:{
            required:true
        },
        label: {
            type:String,
            required: false
        },
        name: {
            type:String,
            required: true,
        },
        id:{
            type:String,
            required:true,
        },
        error: {
            default: null,
        },

        defaultStartTime:{
            type:String,
            default:"09:00:00"
        },

        defaultEndTime:{
            type:String,
            default:"17:00:00"
        },
        calendar:{
            type:Boolean,
            default:true
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
    data() {
        return {
            startDate:null,
            endDate:null
        }
    },
    emits:['update:modelValue'],
    methods:{
        calculateDateTime(type, value) {
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
            this.time = null;
            this.$emit('update:modelValue', null);
        }
    }
}
</script>
