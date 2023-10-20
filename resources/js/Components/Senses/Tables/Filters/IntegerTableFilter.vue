<template>
    <SeInput :id="'filter['+ id+']'" :name="'filter['+id+']'" type="number" :model-value="modelValue" @update:modelValue="$emit('update:modelValue', $event)" label="Value"></SeInput>
</template>
<script>
import SeInput from '../../../Ui/Inputs/SeInput.vue';
export default {
    components:{SeInput},
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