<template>
    <div>
        <!-- <div @click.stop="$modals.push('FileView', { file: proxyFile, canDelete: canDelete, canDownload: downloadable, canEdit: renameable, flush: true})"> -->
        <div @click.stop="$modals.push('GalleryModal', { fileableId: fileableId, fileableType: `${fileableType}s`, openedFile: proxyFile, files: files })">
            <div v-if="proxyFile">
                <FileGridItem
                    v-if="type === 'grid'"
                    :id="index"
                    :file="proxyFile"
                    :size="size"
                    :custom-classes="customClasses"
                    :index="`file${index}`"
                    :condensed="condensed"
                    :removeable="removeable"
                    @remove="removeFile"
                >
                    <div v-if="proxyFile.name && proxyFile.extension">
                        <div :title="proxyFile.name + '.' + proxyFile.extension" class="truncate text-black">{{ proxyFile.name + "." + proxyFile.extension }}</div>
                        <div class="text-sm text-zinc-500">
                            {{ formatDate(proxyFile.created_at, 'dd/MM/yy') }}
                        </div>
                    </div>
                    <div v-else>
                        <div class="ml-1 text-sm text-zinc-500">
                            Pending...
                        </div>
                    </div>
                </FileGridItem>
                <FileDetailListItem
                    v-else-if="type === 'detail-list'"
                    :id="index"
                    :file="proxyFile"
                    :index="`file${index}`"
                >
                    <div v-if="proxyFile.name">{{ proxyFile.name + "." + proxyFile.extension }}</div>
                    <div v-if="proxyFile.pending">File Pending sync</div>
                    <div class="text-sm text-zinc-500">
                        Uploaded by {{ proxyFile.creator?.full_name }}
                    </div>
                    <template #end>
                        <SmallText>{{ formatDateTime(proxyFile.created_at) }}</SmallText>
                    </template>
                </FileDetailListItem>
                <FileBasicListItem
                    v-else-if="type === 'basic-list'"
                    :id="index"
                    :file="proxyFile"
                    :index="`file${index}`"
                >
                    <div v-if="proxyFile.name">{{ proxyFile.name + "." + proxyFile.extension }}</div>
                    <div v-if="proxyFile.pending">File Pending sync</div>
                    <template #end>
                        <SmallText>{{ formatDateTime(proxyFile.created_at) }}</SmallText>
                    </template>
                </FileBasicListItem>
            </div>
        </div>
        <div v-if="proxyFile && selectable" class="flex justify-center mb-6 mt-2">
            <SeToggle
                :id="'select-' + proxyFile.id"
                v-model="file.selected"
                :name="'select-' + proxyFile.id"
                layout="horizontal"
            />
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import SmallText from '../../Ui/Text/SmallText.vue';
import FileGridItem from '../../Ui/Files/FileGridItem.vue';
import FileDetailListItem from '../../Ui/Files/FileDetailListItem.vue';
import FileBasicListItem from '../../Ui/Files/FileBasicListItem.vue';
import formatDate from '../../../Filters/FormatDate';
import formatDateTime from '../../../Filters/FormatDateTime';
import SeToggle from "../../Ui/Inputs/SeToggle.vue";
import EventHub from "../../../Support/EventHub";

export default {
    components: {
        FileGridItem,
        FileDetailListItem,
        FileBasicListItem,
        SmallText,
        SeToggle,
    },
    emits:['remove'],
    props: {
        file: {
            // type: Object
        },
        condensed: {
            type: Boolean,
            default: false,
        },
        fileId: {
            type: Number,
            default: null,
        },
        index: {
            default: 1,
        },
        type: {
            type: String,
            default: 'grid',
        },
        deletable: {
            type: Boolean,
            default: true,
        },
        downloadable: {
            type: Boolean,
            default: true,
        },
        renameable: {
            type: Boolean,
            default: true,
        },
        removeable:{
            type:Boolean,
            default:false
        },
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom', 'xs', 'sm', 'md', 'lg'].includes(
                    value
                );
            },
        },
        customClasses: {
            type: String,
            required: false
        },
        filesToBePublished: {
            type: Array,
            default: () => [],
        },
        relatedForm: { // The form these files belong to in a TaskPublishForm aside.
            type: Object,
            default: () => {}
        },

        //because publish flags almost does what I want, but I can't use it because its too entrenched in many components
        //this is providing a far simpler flag, which one day publishing should use instead of forcing its roots into so many components...
        selectable:{
            type:Boolean,
            default:false
        },
        defaultSelected:{
            type:Boolean,
            default:false,
        },
        files: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            user: window.user,
            proxyFile: this.file,
            publish: true,
        };
    },
    computed: {
        previewUrl() {
            if (this.proxyFile) {
                return (
                    this.proxyFile.preview_url ?? this.proxyFile.original_url
                );
            }
        },
        url() {
            if (this.proxyFile) {
                return this.proxyFile.original_url;
            }
        },
        canDelete() {
            if (this.deletable && (user().can('delete-file') || user().id == this.proxyFile?.created_by)) {
                return true;
            }
            return false;
        },
        canDownload() {
            if (this.downloadable && user().can('download-file')) {
                return true;
            }
            return false;
        },
    },
    watch: {
        fileId() {
            this.getFile();
        },

        file() {
            this.proxyFile = this.file;
        },

        filesToBePublished() {
            this.publish = this.filesToBePublished.includes(this.proxyFile.uuid);
        }
    },
    mounted() {
        if (this.proxyFile) {
            this.publish = this.filesToBePublished.includes(this.proxyFile.uuid);
        }


        if (this.fileId) {
            this.getFile();
        }

        if(this.selectable) {
            if(!this.file.hasOwnProperty('selected')) {
                this.file.selected = false;
            }
        }
    },
    methods: {
        formatDate,
        formatDateTime,

        getFile() {
            this.loading = true;
            axios.get(`/api/v2/files/${this.fileId}`).then((response) => {
                this.proxyFile = response.data;
                this.loading = false;
            });
        },

        emitPublishFile() {
            EventHub.emit('task-publish-file-updated', {id: this.file.id, uuid: this.file.uuid, publish: this.publish, form: this.relatedForm});
        },

        removeFile(file) {
            this.$emit('remove', file);
        },
    },
};
</script>
