<template>
    <SeDateRangeAltPicker :full-width="true" :calendar="false" :id="'filter['+ id+']'" :name="'filter['+id+']'" :model-value="modelValue" @update:modelValue="updateModelValue" label="Value"/>
</template>
<script>
import SeDateRangeAltPicker from '../../../Ui/Inputs/SeDateRangeAltPicker.vue';
export default {
    components:{ SeDateRangeAltPicker },
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