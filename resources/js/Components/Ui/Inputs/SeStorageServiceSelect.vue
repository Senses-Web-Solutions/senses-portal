<template>
    <InputGroup :id="id" :label="label" :error="error" :name="name" :help-text="helpText" inset-validation-icon>
        <div class="mb-6 flex items-center">
            <SecondaryButton :disabled="!currentDirectory || loading" class="translate-x-[1px]" rounded="left" @click="upDirectory">
                <ArrowLeftIcon class="h-4 w-4" />
            </SecondaryButton>
            <SecondaryButton :disabled="loading" rounded="right" @click="homeDirectory">
                <HomeIcon class="h-4 w-4" />
            </SecondaryButton>

            <p v-if="currentDirectory" class="ml-6">
                Folder: {{ currentDirectory }}
            </p>
            <p v-else class="ml-6">Folder: {{ taskFolderPath ?? '/' }}</p>
        </div>
        <div v-if="loading" class="w-full flex justify-center items-center">
            <LoadingIcon class="h-8 w-8" />
        </div>
        <div class="space-y-6">
            <div v-if="!loading" class="space-y-1">
                <SeLabel class="text-primary">Folders</SeLabel>
                <div v-if="directories.length > 0" class="grid grid-cols-1 gap-0">
                    <div
                        v-for="(directory, index) in directories"
                        :key="index"
                        class="flex mb-2 items-center gap-2 cursor-pointer transition-all group rounded-lg border-2 px-2 py-1 hover:border-primary-400"
                        :class="currentDirectory === directory ? ' border-primary [&>*]:!text-primary-500' : ''"
                        @click="() => downDirectory(directory)"
                    >
                        <FolderIcon class="h-5 w-5 text-zinc-300 group-hover:text-primary-400" />
                        <SeLabel class="group-hover:text-primary-500 cursor-pointer">{{ directory.replace('\\', '/').split('/').pop() }}</SeLabel>
                    </div>
                </div>
                <div v-else>
                    <p>There are no folders in this directory</p>
                </div>
            </div>
            <div v-if="!loading" class="space-y-1">
                <SeLabel class="text-primary">Files</SeLabel>
                <div v-if="files.length > 0" class="grid grid-cols-1 gap-0">
                    <div
                        v-for="(file, index) in files"
                        :key="index"
                        class="flex mb-2 items-center gap-2 cursor-pointer w-3/4 transition-all group border-2 px-2 py-1 hover:border-primary-400"
                        :class="currentDirectory === file ? ' border-primary [&>*]:!text-primary-500' : ''"
                        @click="() => selectFile(file)"
                    >
                    {{  }}
                        <DocumentIcon class="h-5 w-5 text-zinc-300 group-hover:text-primary-400" />
                        <SeLabel class="group-hover:text-primary-500 cursor-pointer">{{ fileName(file) }}</SeLabel>
                    </div>
                </div>
                <div v-else>
                    <p>There are no files in this directory</p>
                </div>
            </div>
        </div>
    </InputGroup>
</template>

<script>
import axios from "axios";
import {
    FolderIcon,
    ArrowLeftIcon,
    HomeIcon,
    DocumentIcon
} from "@heroicons/vue/outline";
import InputGroup from "./InputGroup.vue";
import SeLabel from "./SeLabel.vue";
import SecondaryButton from "../Buttons/SecondaryButton.vue";
import LoadingIcon from "../LoadingIcon.vue";

export default {
    components: {
        FolderIcon,
        ArrowLeftIcon,
        HomeIcon,
        DocumentIcon,
        InputGroup,
        SeLabel,
        SecondaryButton,
        LoadingIcon
    },
    props: {
        data: {
            type: Object,
            default: () => { }
        },
        modelValue: {
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: 'Map Select',
        },
        name: {
            type: String,
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        helpText: {
            type: [String, null],
            default: null,
        },
        id: {
            type: String,
            required: true
        },
    },
    emits: ['update:modelValue'],
    data() {
        return {
            directories: [],
            files: [],
            selectedDirectories: [''],
            taskFolderPath: '',
            loading: true
        }
    },
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
        url() {
            let taskID = null;
            if(this.data.task_id) {
                taskID = this.data.task_id;
            }
            else if(this.data.formableId) {
                taskID = this.data.formableId;
            }
            return `/api/v2/storage-service/task/${taskID}`;
        },
        currentDirectory() {
            return this.selectedDirectories[this.selectedDirectories.length - 1];
        }
    },
    mounted() {
        this.loadDirectories();
        this.loadFiles();
        this.loadTaskFolderPathName();
    },
    methods: {
        fileName(file) {
            const lastIndex = file.replace('\\','/').lastIndexOf('/');
            return file.substr(lastIndex).replace('\\', '').replace('/', ''); //show only filename not path to it
        },

        loadTaskFolderPathName() {
            axios({
                method: 'get',
                url: `${this.url}/folder-path-name`,
            }).then(res => {
                this.taskFolderPath = res.data;
            })
        },
        loadDirectories() {
            this.loading = true;
            this.directories = [];
            axios({
                method: 'get',
                url: `${this.url}/directories`,
                params: {
                    directory: this.currentDirectory
                },
            }).then(res => {
                this.directories = res.data;
                this.loading = false;
            })
        },
        loadFiles() {
            this.loading = true;
            this.files = [];
            axios({
                method: 'get',
                url: `${this.url}/files`,
                params: {
                    directory: this.currentDirectory
                },
            }).then(res => {
                this.files = res.data;
                this.loading = false;
            })
        },
        upDirectory() {
            this.selectedDirectories.pop();
            this.$emit('update:modelValue', this.currentDirectory);
            this.loadDirectories();
            this.loadFiles();
        },
        downDirectory(directory) {
            this.selectedDirectories.push(directory);
            this.$emit('update:modelValue', this.currentDirectory);
            this.loadDirectories();
            this.loadFiles();
        },
        homeDirectory() {
            if (this.selectedDirectories.length === 1) {
                return;
            }
            this.selectedDirectories = [''];
            this.$emit('update:modelValue', this.currentDirectory);
            this.loadDirectories();
            this.loadFiles();
        },
        selectFile(file) {
            this.fileSelected = true;
            if (this.selectedDirectories.length === 1) {
                return;
            }
            this.selectedDirectories.push(file);
            this.$emit('update:modelValue', this.currentDirectory);
        }
    }
}
</script>
