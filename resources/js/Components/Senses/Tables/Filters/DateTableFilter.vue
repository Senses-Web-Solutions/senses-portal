<template>
    <SeDatePicker :id="'filter['+ id+']'" :name="'filter['+id+']'" :model-value="modelValue" @update:modelValue="$emit('update:modelValue', $event)" label="Value" :calendar="false" :full-width="true"></SeDatePicker>
</template>
<script>
import SeDatePicker from '../../../Ui/Inputs/SeDatePicker.vue';
export default {
    components:{ SeDatePicker },
    props:{
        id:{
            type:String,
        },
        modelValue:{
            type:String
        }
    },
    emits:['update:modelValue'],
}
</script>