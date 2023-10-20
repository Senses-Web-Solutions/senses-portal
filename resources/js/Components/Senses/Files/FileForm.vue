<template>
<div>
    <FieldGroup v-model="form" :fields="fields"/>
</div>
</template>
<script>
import FieldGroup from '../../Ui/FieldGroup.vue';
export default {
    components:{ FieldGroup },
    props:{
        fileables:{
            type:Array,
            default:() => []
        },
        folder:{
            type:String,
            default:null
        }
    },
    computed: {
        fields() {
            return [
                { label: "Add a File", key: "file", type: "file-picker", fileables: this.fileables, folder: this.folder, showPreview: false }
            ];
        }
    },

    data() {
        return {
            form:{
                file:null,
            }
        };
    }
    //todo make more of a form

}
</script>
