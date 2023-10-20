<template>
    <div class="space-y-2">
        <SeLabel v-if="label" :required="required" :for="id">{{label}}</SeLabel>
        <div
            class="border rounded-md border-zinc-300 shadow-sm cursor-pointer dropzone min-h-[114px] form-input"
            :class="(singleWithPreview && modelValue != null) ? 'hidden' : null"
            :id="id"
            ref="dropzone">
                <div class="dz-message">
                    <EmptyState flush>Drop files here to upload</EmptyState>
                </div>
            </div>
        <div v-if="singleWithPreview && modelValue" class="border rounded-md border-zinc-300 p-4 h-[114px] flex justify-between items-end mt-2">
            <File :fileId="modelValue" :deletable="false" condensed :removeable="false" @remove="removeFile"/>
            <DangerButton size="xs" @click="clearImage">
                <XIcon class="w-5 h-5" />
            </DangerButton>
        </div>
    </div>
</template>

<script>
import EmptyState from '../EmptyState.vue';
import DangerButton from '../Buttons/DangerButton.vue';
import File from '../../Senses/Files/File.vue';
import Dropzone from "dropzone";
import SeLabel from './SeLabel.vue';
import {
    XIcon
} from "@heroicons/vue/outline";
import EventHub from '../../../Support/EventHub';

Dropzone.autoDiscover = false;
export default {
    components: {
        SeLabel,
        File,
        DangerButton,
        XIcon,
        EmptyState,
    },
    props: {
        url: {
            type: String,
            default: '/api/v2/files'
        },
        modelValue: {
            type: [Number, Array],
        },
        acceptedFiles: {
            type: String,
            default: null
        },
        removePreview: {
            type: Boolean,
            default: true
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
        folder: {
            type: String,
            default: null
        },
        multiple: {
            type: Boolean,
            default: true
        },
        pending: {
            type: Boolean, //use to avoid file being moved, if needed for processing
            default: false
        },
        disk: {
            type: String,
            default: 'remote',
            validator(value) {
                // The value must match one of these strings
                return ['remote', 'local'].includes(value)
            }
        }
    },
    data() {
        return {
            dropzone: null
        };
    },
    emits: ['update:modelValue', 'fileUploading', 'fileUploaded'],
    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: this.url,
            acceptedFiles: this.acceptedFiles,
            parallelUploads: 5,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            previewTemplate:
            `<div class="relative grid grid-cols-12 last:border-transparent border-b border-zinc-200">
                <div class="py-2 px-1 col-span-12">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img data-dz-thumbnail class="w-auto h-10" />
                        </div>
                        <div class="space-y-2 truncate ml-4 w-full">
                            <div class="flex justify-between items-end space-x-2">
                                <div class="text-base text-zinc-700 truncate">
                                    <span data-dz-name></span>
                                </div>
                                <div class="text-sm text-zinc-500">
                                    <span data-dz-size></span>
                                </div>
                            </div>
                            <div class="dz-progress-bar opacity-75 w-full">
                                <div class="flex flex-grow bg-zinc-200 rounded" role="progressbar">
                                    <div data-dz-uploadprogress class="w-0 bg-primary-700 rounded h-1 text-center transition" style="transition: width 0.5s">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dz-error-message hidden text-red-600"><span data-dz-errormessage></span></div>
            </div>`,
            success: (file, response) => {
                if (Array.isArray(this.modelValue)) {
                    this.$emit("update:modelValue", [...this.modelValue, response.id]);
                } else {
                    this.$emit("update:modelValue", response.id);
                }

                this.$emit('fileUploaded', response);
                if (this.removePreview) {
                    this.dropzone.removeFile(file);
                }
            },
        });

        this.dropzone.on("sending", (file, xhr, data) => {
            this.$emit('fileUploading', data);
            EventHub.emit('file-uploading');
            if (!this.multiple) {
                if (this.dropzone.files.length > 1) {
                    this.dropzone.removeFile(this.dropzone.files[0]);
                }
            }

            if (this.fileables) {
                this.fileables.forEach((fileable, index) => {
                    data.append("fileables[" + index + "][fileable_id]", fileable.id);
                    data.append("fileables[" + index + "][fileable_type]", fileable.type);
                });
            }
            if (this.folder) {
                data.append("folder", this.folder);
            }
            if (this.disk) {
                data.append("disk", this.disk);
            }
            if (this.pending) {
                data.append("pending", 1);
            }
        });

        this.dropzone.on("addedfile", (file) => {
            if (!file.type.match(/image.*/)) {
                this.dropzone.emit("thumbnail", file, "/images/default-file-thumbnail.svg");
            }
        });

        this.dropzone.on('queuecomplete', () => {
            this.$emit('queueUploaded');
            EventHub.emit('files-queue-complete');
        })
    },
    unmounted() {
        this.dropzone.destroy();
    },
    methods: {
        removeFile(file) {
            console.log('remove', file);
        },
        clearImage() {
            if (this.dropzone.files[0]) {
                this.dropzone.removeFile(this.dropzone.files[0]);
            }
            this.$emit("update:modelValue", null);
        },
    },
    computed: {
        singleWithPreview() {
            return !this.removePreview && !this.multiple;
        }
    }
}
</script>
