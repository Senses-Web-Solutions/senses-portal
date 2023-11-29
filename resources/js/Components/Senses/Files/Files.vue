<template>
    <CollapseTransition>
        <FileForm
            v-if="addFile && state.is(PageState.IDLE)"
            class="border-t border-zinc-200 px-5 py-5"
            :fileables="formFileables"
            :folder="currentFolder"
            :showFiles="false"
            :remove-preview="true" />
    </CollapseTransition>
    <CollapseTransition>
        <IndeterminateLoadingBar v-if="state.is(PageState.LOADING)" />
        <div v-else>
            <div v-if="!condensed && folder" class="flex items-center justify-between border-b">
                <div class="flex py-3 px-5 text-zinc-600 space-x-0.5">
                    <div class="cursor-pointer" @click="up(0)">
                        {{ TitleCase(fileableType) }} {{ fileableId }}  /
                    </div>
                    <div
                        v-for="(folder, index) in path"
                        class="capitalize"
                        :key="`filesChunkPath${folder}${index}`"
                        @click="up(index)">
                        <div v-if="folder != '/'">
                            {{ folder }}  /
                        </div>
                        <!-- <span v-if="index !== path.length - 1" class="px-3 text-zinc-400">{{ index === 0 ? '&nbsp;' : '/' }}</span> -->
                    </div>
                </div>
                <div class="flex items-center py-1 pr-5">
                    <ButtonGroup v-if="!isPublishForm">
                        <!-- <SecondaryButton
                            size="xs"
                            @click="alert('download not implemented')">
                            <FolderDownloadIcon class="w-4 h-4"/>
                        </SecondaryButton> -->
                        <div class="relative z-0 inline-flex rounded-md shadow-sm">
                            <SecondaryButton
                                class="translate-x-[2px]"
                                :class="
                                    view === 'basic-list' ? `!bg-zinc-200` : ''
                                "
                                @click="view = 'basic-list'"
                                size="xs"
                                rounded="left">
                                <ViewListIcon class="h-4 w-4" />
                            </SecondaryButton>

                            <SecondaryButton
                                class="translate-x-[1px]"
                                :class="
                                    view === 'detail-list' ? `!bg-zinc-200` : ''
                                "
                                @click="view = 'detail-list'"
                                size="xs"
                                rounded="none">
                                <ViewBoardsIcon class="h-4 w-4 rotate-90" />
                            </SecondaryButton>

                            <SecondaryButton
                                :class="view === 'grid' ? `!bg-zinc-200` : ''"
                                @click="view = 'grid'"
                                size="xs"
                                rounded="right">
                                <ViewGridIcon class="h-4 w-4" />
                            </SecondaryButton>
                        </div>
                        <SecondaryButton size="xs" :disabled="path.length === 1" @click="up">
                            <ArrowUpIcon class="h-4 w-4" />
                        </SecondaryButton>
                        <SecondaryButton id="create-file" v-if="canAdd && user().can('create-file')" size="xs" @click="toggleForm">
                            <div class="text-sm">Add</div>
                        </SecondaryButton>
                    </ButtonGroup>
                </div>
            </div>
            <div v-if="visibleFiles && Object.keys(visibleFiles).length">
                <div v-bind="$attrs" :class="{
                        'flex flex-wrap gap-5': view === 'grid',
                        'divide-y divide-zinc-200':
                            view !== 'grid',
                        'p-5': view === 'grid' && !condensed,
                    }">
                    <template v-for="(file, key) in visibleFiles">
                        <Folder
                            v-if="Array.isArray(file) && file.length > 0"
                            :folder="file"
                            :index="key"
                            :type="view"
                            @click="enter(key)"
                            :key="'folder-' + key"></Folder>
                        <File
                            v-else-if="!Array.isArray(file)"
                            :data-file-id="`${file.id}`"
                            :file="file"
                            :index="key"
                            :type="view"
                            :key="'file-' + key"
                            :condensed="condensed"
                            :is-publish-form="isPublishForm"
                            :files-to-be-published="filesToBePublished"
                        />
                    </template>
                </div>
            </div>
            <EmptyState v-else item="Files" />
        </div>
    </CollapseTransition>
</template>

<script>
import {
    FolderIcon,
    ArrowUpIcon,
    DocumentIcon,
    ViewGridIcon,
    ViewListIcon,
    ViewBoardsIcon,
    HomeIcon,
} from '@heroicons/vue/solid';
import {
    FolderDownloadIcon,
    PlusIcon
} from '@heroicons/vue/outline';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import {
    get
} from 'lodash-es';
import axios from 'axios';
import FileForm from './FileForm.vue';
import File from './File.vue';
import Folder from './Folder.vue';
import pluralize from 'pluralize';
import PageState from '../../../States/PageState';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import CollapseTransition from '../../Ui/Transitions/CollapseTransition.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import FileMaintainer from '../../../Support/Maintainers/FileMaintainer.js';

import TitleCase from '../../../Filters/TitleCase.js';

//todo task version should only pass the data in? Its also got templates that rely on slots for info but uses url in the templates?  (component is and view stuff within each type!)
//todo add pusher/maintainer bits, backend should be broadcasting an event similar to comments
//todo pusher works but doesn't create folder if folder does not already exist - needs to do this?
export default {
    components: {
        EmptyState,
        FileForm,
        ArrowUpIcon,
        ViewGridIcon,
        ViewListIcon,
        ViewBoardsIcon,
        ButtonGroup,
        SecondaryButton,
        File,
        Folder,
        CollapseTransition,
        IndeterminateLoadingBar,
    },
    props: {
        fileableType: {
            type: String,
            default: null,
        },
        fileableId: {
            type: Number,
            default: null,
        },
        files: {
            type: Array,
        },
        condensed: {
            default: false,
        },
        folder: {
            default: true,
        },
        showForm: {
            type: Boolean,
            default: false,
        },
        canAdd: {
            type: Boolean,
            default: true,
        },
        publicOnly: {
            type: Boolean,
            default: false,
        },
        isPublishForm: {
            type: Boolean,
            default: false,
        },
        filesToBePublished: {
            type: Array,
            default: () => []
        },
        defaultView: {
            type: String,
            default: ''
        }
    },

    watch: {
        showForm(newVal, oldVal) {
            this.addFile = newVal;
        },
    },

    data() {
        return {
            user: window.user,
            formFileables: [{
                id: this.fileableId,
                type: this.fileableType,
            }, ],
            path: ['/'],
            proxyFiles: {},
            state: new PageState(),
            PageState,
            addFile: false,
            view: 'basic-list',
            maintainer: null,
        };
    },

    computed: {
        url(){
            var url = '/api/v2/' + pluralize(this.fileableType) + '/' + this.fileableId + '/files?format=folders';
            if(this.publicOnly){
                url += '&filter[public_exact]=1'
            }
            return url;
        },
        visibleFiles() {
            const actualPath = [...this.path];
            actualPath.shift(); // remove task XXXXXX
            if (actualPath.length === 0) {
                let files = {};

                //remove empty folders
                Object.keys(this.proxyFiles).forEach((key) => {
                    const file = this.proxyFiles[key];
                    if (
                        !Array.isArray(file) ||
                        (Array.isArray(file) && file.length > 0)
                    ) {
                        files[key] = file;
                    }
                });

                return files;
            }
            return get(this.proxyFiles, actualPath.join('.'));
        },

        currentFolder() {
            const currentFolder = this.path
                .filter((path, index) => index !== 0)
                .join('/');

            return currentFolder !== '' ? currentFolder : null;
        },
    },

    mounted() {
        this.addFile = this.showForm;

        if (this.defaultView) {
            this.view = this.defaultView;
        }
        if (this.fileableType && this.fileableId) {
            this.loadFiles();
            // this.loadMaintainer();
        } else {
            this.proxyFiles = this.files;
            this.state.set(PageState.IDLE);
        }
    },

    methods: {
        TitleCase,
        loadFiles() {
            axios.get(this.url)
                .then(async (response) => {
                    this.proxyFiles = response.data;

                    if (this.proxyFiles['']) {
                        const rootFiles = this.proxyFiles[''];
                        rootFiles.forEach((file) => {
                            this.proxyFiles[file.id] = file;
                        });

                        delete this.proxyFiles[''];
                    }

                    await this.loadMaintainer();
                    this.state.set(PageState.IDLE);
                })
                .catch(() => this.state.set(PageState.ERROR));
        },
        enter(folder) {
            this.path.push(folder);
        },
        up(index = null) {
            if (index && index == this.path.length - 1) {
                return; //ignore removing if clicked from current level
            }
            if (this.path.length > 1) {
                this.path.pop(); //todo currently breadcrumbs only go up one, where multilevel should take you to correct level
            }
        },
        toggleForm() {
            this.addFile = !this.addFile;
        },
        async loadMaintainer(force = false) {
            if (this.maintainer) {
                if (this.maintainer) {
                    this.maintainer.destroy();
                }
                return;
            }

            this.maintainer = new FileMaintainer(
                this.proxyFiles,
                this.fileableType,
                this.fileableId
            );
        },
    },

    unmounted() {
        if (this.maintainer) {
            this.maintainer.destroy();
        }
    },
};
</script>
