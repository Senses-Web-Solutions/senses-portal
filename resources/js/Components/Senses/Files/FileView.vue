<template>
    <div class="block w-auto p-6 overflow-hidden rounded-lg" :style="style">
        <template v-if="!fileLoading">
            <div v-if="type == 'pending'">
                <div class="text-center text-zinc-400">
                    <DeviceMobileIcon class="inline-block w-8 h-8" />
                    <p class="py-5 text-sm">This file is awaiting sync.</p>
                </div>
            </div>

            <img v-if="type == 'image'" class="object-scale-down mx-auto rounded-md select-none w-[80vw] h-[70vh]" :src="data?.file.original_url ?? data?.file.preview_url" />
            <iframe v-if="type == 'pdf'" :src="data?.file.original_url" width="1200" height="800" />
            <video v-if="type == 'video'" :src="data?.file.original_url" width="1200" class="aspect-video" controls />

            <div v-if="type == 'unknown'" class="flex items-center justify-center mx-auto bg-zinc-100 rounded-md select-none w-[800px] h-[800px]">
                <div class="text-center text-zinc-400">
                    <DocumentDownloadIcon class="inline-block w-8 h-8" />
                    <p class="py-5 text-sm">Unable to show preview</p>
                </div>
            </div>

            <div v-if="type != 'pending'" class="flex justify-between pt-6 h-16">
                <template v-if="!editingFile">
                    <div class="flex items-center space-x-4">
                        <div class="flex flex-col mr-8">
                            {{ this.file?.name }}.{{ data?.file?.extension }}
                            <SmallText>Uploaded at: {{ formatDateTime(data?.file?.created_at) }}</SmallText>
                        </div>
                        <SuccessBadge v-if="file.public">
                            Public
                        </SuccessBadge>
                        <SuccessBadge v-if="file.app_visible">
                            App Visible
                        </SuccessBadge>
                    </div>
                    <ButtonGroup>
                        <SecondaryButton v-if="data.canEdit" @click="editingFile = true">
                            Edit
                        </SecondaryButton>

                        <a v-if="data.canDownload" download :href="data?.file?.original_url" target="_blank">
                            <SecondaryButton>Download</SecondaryButton>
                        </a>

                        <DeleteButton v-if="data.canDelete" :redirect="false" :id="data?.file.id" singularModel="file" :data="data?.file" pluralModel="files" :close-asides="false" />
                    </ButtonGroup>
                </template>
                <template v-else>
                    <div class="flex space-x-4 items-center">
                        <SeValidation :error="error" name="name">
                            <div class="flex items-center space-x-4">
                                <SeInput type="text" id="name" name="name" class="w-96" v-model="file.name" />
                            </div>
                        </SeValidation>
                        <SeValidation :error="error" name="app_visible">
                            <div class="flex items-center space-x-4">
                                <SeToggle v-model="file.app_visible" id="app_visible" name="app_visible" label="Show on App" layout="horizontal"></SeToggle>
                            </div>
                        </SeValidation>
                        <SeValidation :error="error" name="public">
                            <div class="flex items-center space-x-4">
                                <SeToggle v-model="file.public" id="public" name="public" label="Public" layout="horizontal"></SeToggle>
                            </div>
                        </SeValidation>
                        <LoadingIcon v-if="editLoading" class="w-5 h-5 text-primary"></LoadingIcon>
                    </div>
                    <ButtonGroup>
                        <SecondaryButton :disabled="editLoading" @click="editingFile = false">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="editLoading" @click="submitEdit">Save</PrimaryButton>
                    </ButtonGroup>
                </template>
            </div>
        </template>
        <template v-else>
            <div class="flex items-center justify-center object-scale-down mx-auto rounded-md select-none w-[80vw] h-[75vh]">
                <LoadingIcon class="w-8 h-8 text-primary"></LoadingIcon>
            </div>
        </template>
    </div>
</template>

<script>
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import SeValidation from '../../Ui/Inputs/SeValidation.vue';
import Error from '../../Ui/Errors/Error.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeToggle from '../../Ui/Inputs/SeToggle.vue';
import SmallText from '../../Ui/Text/SmallText.vue';
import StrongText from '../../Ui/Text/StrongText.vue';
import File from './File.vue';
import Modal from "../../../Mixins/Modal";
import ButtonGroup from "../../Ui/Buttons/ButtonGroup.vue";
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import DeleteButton from "../../Ui/Buttons/DeleteButton.vue";
import SuccessBadge from "../../Ui/Badges/SuccessBadge.vue";
import InfoButton from "../../Ui/Buttons/InfoButton.vue";
import axios from "axios";
import {
    DocumentDownloadIcon,
    DeviceMobileIcon,
    PencilAltIcon,
} from '@heroicons/vue/outline';
import EventHub from '../../../Support/EventHub';
import formatDateTime from '../../../Filters/FormatDateTime.js';

//todo styling in this component needs sorting
export default {
    mixins: [Modal],
    components: {
        ButtonGroup,
        SecondaryButton,
        DocumentDownloadIcon,
        DeviceMobileIcon,
        SeToggle,
        DeleteButton,
        SuccessBadge,
        SmallText,
        SeInput,
        PrimaryButton,
        SeValidation,
        LoadingIcon,
    },

    //data
    data() {
        return {
            editingFile: false,
            file: {
                name: "",
                public: false,
                app_visible: false
            },
            error: null,
            fileLoading: false,
            editLoading: false,
        }
    },

    computed: {
        type() {
            if (this.data?.file?.pending) {
                return 'pending';
            }
            if (this.data?.file && this.data?.file.mime_type && this.data?.file.mime_type.startsWith('image/')) {
                return 'image';
            }
            if (this.data?.file && this.data?.file.mime_type && this.data?.file.mime_type == 'application/pdf') {
                return 'pdf';
            }
            if (this.data?.file && this.data?.file.mime_type && this.data?.file.mime_type.startsWith('video/')) {
                return 'video';
            }
            return 'unknown';
        },
        style() {
            // if (this.type == 'image') {
            //     return {
            //         'height': '80vh',
            //         'max-width': '80vw'
            //     };
            // }
            return {}
        },
    },

    created() {
        EventHub.on('file-deleted', (data) => {
            this.$modals.pop();
        });
    },
    mounted() {
        if (!this.data?.file) {
            this.fileLoading = true;
            axios.get('/api/v2/files/' + this.data.id)
                .then((response) => {
                    this.data.file = response.data;
                    this.file = response.data;
                    this.fileLoading = false;
                })
                .catch((error) => {
                    this.error = error.response.data;
                    this.fileLoading = false;
                })
        }

        this.file = this.data?.file;
    },
    unmounted() {
        EventHub.off('file-deleted');
    },
    methods: {
        formatDateTime,
        download() {
            axios.get('/files/' + this.data?.file.id + '/download');
        },

        submitEdit() {
            this.editLoading = true;
            axios.patch('/api/v2/files/' + this.data?.file.id, this.file)
                .then((response) => {
                    EventHub.emit('file-updated', response.data);
                    this.editingFile = false;
                    this.editLoading = false;
                })
                .catch((error) => {
                    this.error = error.response.data;
                    this.editLoading = false;
                })
        }
    }
};
</script>
