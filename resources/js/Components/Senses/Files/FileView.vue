<template>
    <div class="block w-auto p-6 overflow-hidden rounded-lg">
        <div class="flex flex-row justify-between space-x-6">
            <!-- Image -->
            <div class="w-full">
                <div v-if="type == 'pending'">
                    <div class="text-center text-gray-400">
                        <DeviceMobileIcon class="inline-block w-8 h-8" />
                        <p class="py-5 text-sm">This file is awaiting sync.</p>
                    </div>
                </div>
                <img 
                    v-if="type == 'image'" 
                    class="object-scale-down mx-auto rounded-lg overflow-hidden select-none h-[70vh]" 
                    :class="{
                        'rotate-90': rotation == 90,
                        'rotate-180': rotation == 180,
                        '-rotate-90': rotation == 270,
                    }"
                    :src="file.original_url ?? file.preview_url"
                />

                <iframe 
                    v-if="type == 'pdf'" 
                    class="object-scale-down mx-auto rounded-md select-none h-[70vh]" 
                    :src="file?.original_url" 
                    :width="iframeWidth" 
                    height="800" 
                />

                <video 
                    v-if="type == 'video'" 
                    :src="file?.original_url" 
                    width="1200" 
                    class="aspect-video" 
                    controls 
                />
            </div>

            <!-- Meta Data -->
            <FileMeta v-if="showMeta" :file="file" />
        </div>
    </div>
    <!-- <div class="absolute bottom-6 right-6 w-1/4 bg-white mt-4"> -->
    <div class="flex flex-row justify-between">
        <div v-if="!editingFile" class="flex items-center space-x-4">
            <div class="text-black flex flex-col mr-8">
                {{ file?.name }}.{{ file?.extension }}
                <SmallText>Uploaded at: {{ formatDateTime(file?.created_at) }}</SmallText>
            </div>
            <SuccessBadge v-if="file.public">
                Public
            </SuccessBadge>
            <SuccessBadge v-if="file.app_visible">
                App Visible
            </SuccessBadge>
        </div>

        <div v-if="!editingFile" class="flex flex-row justify-end items-center space-x-3">
            <slot name="actions"></slot>
            <SecondaryButton @click="editingFile = true">
                Edit
            </SecondaryButton>
            <a download :href="file?.original_url" target="_blank">
                <SecondaryButton>Download</SecondaryButton>
            </a>
            <DeleteButton
                :id="file.id"
                :redirect="false"
                singular-model="file"
                :data="file"
                plural-model="files"
                :close-asides="false" 
            />
        </div>
        <div v-else class="flex flex-row w-full">
            <div class="flex w-full justify-between items-center">
                <div class="flex items-center space-x-4">
                    <SeValidation :error="error" name="name">
                        <div class="flex items-center space-x-4">
                            <SeInput
                                id="name"
                                v-model="file.name"
                                type="text"
                                name="name"
                                class="w-96" 
                            />
                        </div>
                    </SeValidation>
                    <SecondaryButton @click="rotateFile">
                        Rotate
                    </SecondaryButton>
                    <SeValidation :error="error" name="app_visible">
                        <div class="flex items-center space-x-4">
                            <SeToggle
                                id="app_visible"
                                v-model="file.app_visible"
                                name="app_visible"
                                label="Show on App"
                                layout="horizontal"
                            />
                        </div>
                    </SeValidation>
                    <SeValidation :error="error" name="public">
                        <div class="flex items-center space-x-4">
                            <SeToggle
                                id="public"
                                v-model="file.public"
                                name="public"
                                label="Public"
                                layout="horizontal"
                            />
                        </div>
                    </SeValidation>
                    <LoadingIcon v-if="editLoading" class="w-5 h-5 text-primary"></LoadingIcon>
                </div>
            </div>
            <ButtonGroup>
                <SecondaryButton :disabled="editLoading" @click="editingFile = false">Cancel</SecondaryButton>
                <PrimaryButton :disabled="editLoading" @click="submitEdit">Save</PrimaryButton>
            </ButtonGroup>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import {
    DeviceMobileIcon,
} from '@heroicons/vue/outline';

import FileMeta from './FileMeta.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import DeleteButton from '../../Ui/Buttons/DeleteButton.vue';
import SuccessBadge from '../../Ui/Badges/SuccessBadge.vue';
import SmallText from '../../Ui/Text/SmallText.vue';
import SeValidation from '../../Ui/Inputs/SeValidation.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeToggle from '../../Ui/Inputs/SeToggle.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';

import formatDateTime from '../../../Filters/FormatDateTime';
import EventHub from '../../../Support/EventHub';
import user from '../../../Support/user';

export default {
    components: {
        DeviceMobileIcon,
        FileMeta,
        PrimaryButton,
        SecondaryButton,
        DeleteButton,
        SuccessBadge,
        SmallText,
        SeValidation,
        SeInput,
        SeToggle,
        LoadingIcon,
        ButtonGroup,
    },
    props: {
        file: {
            type: Object,
            required: true,
        },
        showMeta: {
            type: Boolean,
            default: false,
        },
        gallery: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            editingFile: false,
            editLoading: false,
            error: null,

            rotation: 0,
        };
    },
    computed: {
        type() {
            if (!this.file || !this.file.mime_type) {
                return this.file?.pending ? 'pending' : 'unknown';
            }

            if (this.file.mime_type.startsWith('image/')) {
                return 'image';
            }

            if (this.file.mime_type === 'application/pdf') {
                return 'pdf';
            }

            if (this.file.mime_type.startsWith('video/')) {
                return 'video';
            }

            return 'unknown';
        },
        windowWidth() {
            return window.innerWidth;
        },
        iframeWidth() {
            if (this.windowWidth < 1500 && this.showMeta) {
                return 700;
            }

            if (this.windowWidth < 1500) {
                return 1000;
            }

            if (this.windowWidth < 1800 && this.showMeta) {
                return 1000;
            }

            return 1200;
        },
        canDelete() {
            return user().can('delete-file') || user().id === this.file?.created_by;
        },
        canDownload() {
            return user().can('download-file');
        },
        canEdit() {
            return user().can('edit-file') || user().id === this.file?.created_by;
        },
    },
    created() {
        EventHub.on('file-deleted', () => {
            this.$modals.pop();
        });
    },
    beforeUnmount() {
        EventHub.off('file-deleted');
    },
    methods: {
        formatDateTime,

        submitEdit() {
            this.editLoading = true;

            // This is due to imagick rotating counter clockwise
            this.file.rotation = this.rotation;

            axios.patch(`/api/v2/files/${this.file.id}`, this.file)
                .then((response) => {
                    EventHub.emit('file-updated', response.data);
                    this.editingFile = false;
                    this.editLoading = false;
                })
                .catch((error) => {
                    this.error = error.response.data;
                    this.editLoading = false;
                })
        },

        rotateFile() {
            this.rotation += 90;

            if (this.rotation >= 360) {
                this.rotation = 0;
            }
        }
    }
}
</script>