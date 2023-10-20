<template>
    <SeSelect
        :id="'filter['+ id+']'" 
        :name="'filter['+id+']'"
        :model-value="modelValue" 
        @update:modelValue="$emit('update:modelValue', $event)"
        :options="options"
        field="id"
        label="Value"
    ></SeSelect>
</template>
<script>
import SeSelect from '../../../Ui/Inputs/SeSelect.vue';

export default {
    components:{SeSelect },
    props:{
        id:{
            type:String,
        },
        modelValue:{
            type:String
        }
    },
    emits:['update:modelValue'],
    data() {
        return {
            options:[
                { title: "True", id:1 },
                { title: "False", id:0 },
            ]
        }
    }
}
</script>