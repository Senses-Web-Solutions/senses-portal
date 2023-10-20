<template>
    <SeDateTimeRangePicker :calendar="false" :id="'filter['+ id+']'" :name="'filter['+id+']'" :model-value="modelValue" @update:modelValue="updateModelValue" label="Value" default-start-time="00:00:00" default-end-time="23:59:59"></SeDateTimeRangePicker>
</template>
<script>
import SeDateTimeRangePicker from '../../../Ui/Inputs/SeDateTimeRangePicker.vue';
export default {
    components:{ SeDateTimeRangePicker },
    props:{
        id:{
            type:String,
        },
        modelValue:{
            type:String
        }
    },
    emits:['update:modelValue'],
    methods:{
        updateModelValue(value) {
            this.$emit('update:modelValue', [value.start_date, value.end_date].join(','));
        }
    }
}
</script>