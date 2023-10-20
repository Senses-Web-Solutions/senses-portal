<template>
    <div class="relative">
        <IndeterminateLoadingBar
            v-if="loading && !silentLoad"
            class="absolute left-0 right-0 z-10"
        />

        <div
            v-if="proxyFiles?.length"
            class="mt-4 flex flex-wrap"
        >
            <File
                v-for="file in proxyFiles"
                :key="file.id"
                class="mr-2 mb-2"
                :size="size"
                :condensed="condensed"
                :file="file"
                :deletable="deletable"
                :selectable="selectable"
                :default-selected="file.selected"
                :downloadable="downloadable"
                :is-publish-form="isPublishForm"
                :files-to-be-published="filesToBePublished"
                :related-form="relatedForm"
            />
        </div>
        <EmptyState v-if="proxyFiles && proxyFiles.length == 0 && showEmpty">No Files Found.</EmptyState>
    </div>
</template>
<script>
import axios from 'axios';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import File from './File.vue';
import EventHub from '../../../Support/EventHub';
import EmptyState from '../../Ui/EmptyState.vue';

export default {
    components: {
        IndeterminateLoadingBar,
        File,
        EmptyState,
    },
    emits:['update:modelValue'],
    props: {
        url: {
            type: String,
            default: null,
        },
        silentLoad: {
            type: Boolean,
            default: false,
        },
        showEmpty: {
            type: Boolean,
            default: true,
        },
        condensed: {
            type: Boolean,
            default: false,
        },
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom', 'xs', 'sm', 'md', 'lg'].includes(value);
            },
        },
        deletable: {
            type: Boolean,
            default: true,
        },
        selectable: {
            type: Boolean,
            default: false,
        },
        downloadable: {
            type: Boolean,
            default: true,
        },
        isPublishForm: { // Is this being viewed in a TaskPublishForm aside?
            type: Boolean,
            default: false,
        },
        filesToBePublished: {
            type: Array,
            default: () => [],
        },
        // The form these files belong to in a TaskPublishForm aside.
        relatedForm: {
            type: Object,
            default: () => {}
        },
        modelValue: {
            type:Array,
            default: () => []
        },
    },
    data() {
        return {
            error: null,
            loading: true,
            files: null,
        };
    },
    computed:{
        proxyFiles() {
            let proxyFiles = [];
            if(this.files) {
                proxyFiles = proxyFiles.concat(this.files);
            }

            if(this.modelValue) {
                proxyFiles = proxyFiles.concat(this.modelValue);
            }

            return proxyFiles;
        }
    },
    created() {
        this.load();
        EventHub.on('file-deleted', this.onFileDeleted);
    },
    methods: {
        onFileDeleted(data) {
            if (this.files.find((file) => file.id === data)) {
                this.load();
            }
        },
        load() {
            this.loading = true;

            if (!this.url) {
                this.loading = false;
                return;
            }

            axios
                .get(this.url)
                .then((response) => {
                    this.loading = false;

                    if (response.data?.data) {
                        this.files = response.data.data;
                    } else {
                        this.files = response.data
                    }
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });
        },
    },
};
</script>