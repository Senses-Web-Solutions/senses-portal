<template>
    <SeDateTimePicker 
        :id="'filter['+ id+']'" 
        :name="'filter['+id+']'" 
        :model-value="modelValue" 
        @update:modelValue="$emit('update:modelValue', $event)" 
        label="Value" />
</template>
<script>
import SeDateTimePicker from '../../../Ui/Inputs/SeDateTimePicker.vue';

export default {
    components:{SeDateTimePicker},
    props:{
        id:{
            type:String,
        },
        modelValue:{
            type:String
        }
},
    emits:['update:modelValue'],
    mounted() {
        console.log('mounted');
    }
}
</script>