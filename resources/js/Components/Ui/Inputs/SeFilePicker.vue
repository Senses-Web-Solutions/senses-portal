<template>
    <div class="space-y-2">
        <SeLabel v-if="label" :required="required" :for="id">{{label}}</SeLabel>
        <div class="flex flex-wrap" v-if="files && showPreview">
            <File class="mb-2 mr-2" v-for="file in files" :key="file.id" :file="file" :removeable="true" @remove="removeFile"/>
        </div>
        <SeFileUpload v-model="proxyValue" v-bind="fileUploadProps" @file-uploaded="addFile" :disk="disk" :pending="pending"/>
    </div>
</template>
<script>
import SeFileUpload from './SeFileUpload.vue';
import SeLabel from './SeLabel.vue';
import File from "../../Senses/Files/File.vue"; //todo this should be moved to UI
import axios from 'axios';
import EventHub from '../../../Support/EventHub';

export default {
    components: {
        SeFileUpload,
        SeLabel,
        File
    },
    props:{
        url: {
            type: String,
            default: '/api/v2/files'
        },
        showUrl: {
            type: String,
            default: null
        },
        modelValue: {
            type: [Object, Array],
        },
        acceptedFiles: {
            type: String,
            default: null
        },

        label: {
            type: String,
            required: false
        },
        id: {
            type: String,
            required: true,
        },
        required: {
            type: Boolean,
            default: false
        },
        fileables: {
            type: Array,
            default: null
        },
        folder:{
            type:String,
            default:null
        },
        showPreview: {
            type: Boolean,
            default: true
        },

        modelId:{
            default:null,
        },
        editing:{
            type:Boolean,
            default:false
        },
        multiple:{
            type:Boolean,
            default:true
        },
        pending:{
            type:Boolean, //use to avoid file being moved, if needed for processing
            default:false
        },
        disk:{
            type:String,
            default:'remote',
            validator(value) {
                // The value must match one of these strings
                return ['remote', 'local'].includes(value)
            }
        },
        showFiles: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {
            file_ids:[],
            files:[]
        }
    },
    computed:{
        proxyValue:{
            get() {
                return this.modelValue;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
        fileUploadProps() {
            const props = { ...this.$props }
            delete props.label;
            return props;
        }
    },
    watch:{
        modelId(newVal, oldVal) {
            this.loadFiles();
        },
        // modelValue(newVal, oldVal) {
        //     this.loadFiles();
        // }
    },
    created() {
        this.loadFiles();
        EventHub.on('file-deleted', (id) => {
            this.files = this.files.filter(file => file.id !== id);
        });
    },
    unmounted() {
        EventHub.off('file-deleted');
    },
    methods:{
        loadFiles() {

            let url = this.showUrl ?? '/api/v2/files/unlisted/{id}';

            let ids = [];
            if(this.modelId) {
                ids.push(this.modelId);
            }

            if(this.modelValue) {
                if(Array.isArray(this.modelValue) && this.modelValue.length > 0) {
                    ids = ids.concat(this.modelValue);
                }
                else if(!Array.isArray(this.modelValue)) {
                    ids.push(this.modelValue);
                }
            }

            url = url.replace('{id}', ids.join(','));

            if(ids.length > 0) {
                axios.get(url).then(response => {
                    this.files = response.data.data;
                });
            }
        },
        addFile(file) {
            if(this.multiple) {
                this.files.push(file);
            }
            else {
                this.files = [file];
            }
        },
        removeFile(removedFile) {
            this.files = this.files.filter(file => file.id !== removedFile.id);
            this.proxyValue = this.files.map(file => file.id);
        },
    }
}
</script>
