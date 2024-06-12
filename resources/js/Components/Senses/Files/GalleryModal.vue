<template>
    <div class="w-[80vw] max-w-[1700px]">
        <div v-if="isLoading" class="h-[70vh] w-full flex items-center justify-center">
            <LoadingIcon class="w-8 h-8 text-primary"></LoadingIcon>
        </div>
        <Gallery v-if="isFiles" :opened-file="openedFile" :files="files" />
    </div>
</template>
<script>
import axios from 'axios';
import pluralize from 'pluralize';

import Gallery from './Gallery.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';

export default {
    components: {
        Gallery,
        LoadingIcon
    },
    props: {
        data: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            fetchedFiles: [],
            isLoading: false,
        };
    },
    computed: {
        // Props because we've done it through this modal thing
        files() {
            return this.data?.files ?? this.fetchedFiles;
        },

        fileableType() {
            return this.data?.fileableType;
        },

        fileableId() {
            return this.data?.fileableId;
        },

        openedFile() {
            return this.data?.openedFile;
        },

        // Computed
        url() {
            // console.log(this.fileableId, this.fileableType);
            return this.data?.url ?? `/api/v2/${this.fileableType}/${this.fileableId}/files?limit=250`;
        },

        // Booleans
        shouldFetchFiles() {
            if (this.data?.files) {
                return false;
            }

            return true;
        },

        isFiles() {
            return this.files.length > 0;
        },
    },
    mounted() {
        this.loadFiles();
    },
    methods: {
        loadFiles() {
            if (this.shouldFetchFiles) {
                this.isLoading = true;
                axios.get(this.url)
                    .then((response) => {
                        this.fetchedFiles = response.data.data;
                        this.isLoading = false;
                    })
                    .catch((error) => {
                        console.error(error);
                        this.isLoading = false;
                    });
            }
        },
    },
}
</script>