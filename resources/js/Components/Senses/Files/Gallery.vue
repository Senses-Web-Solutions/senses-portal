<template>
    <div class="w-full">
        <FileView :file="currentFile" :show-meta="viewMeta" gallery>
            <template #actions>
                <GalleryActions
                    v-model:current-file-index="currentFileIndex"
                    v-model:view-meta="viewMeta"
                    :files-length="filesLength"
                />
            </template>
        </FileView>
    </div>
</template>

<script>

import FileView from './FileView.vue';
import GalleryActions from './GalleryActions.vue';

export default {
    components: {
        FileView,
        GalleryActions,
    },
    props: {
        files: {
            type: Array,
            default: () => [],
        },
        showMeta: {
            type: Boolean,
            default: false,
        },
        openedFile: {
            type: Object,
            default: () => null,
        },
    },
    data() {
        return {
            currentFileIndex: 0,
            viewMeta: this.showMeta
        };
    },
    computed: {
        filesLength() {
            return this.files.length;
        },

        currentFile() {
            return this.files[this.currentFileIndex];
        },
    },
    created() {
        if (this.openedFile) {
            this.currentFileIndex = this.files.findIndex(file => file.id === this.openedFile.id);
        }
    },
}
</script>