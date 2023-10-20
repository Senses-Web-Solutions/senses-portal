<template>
    <div :style="{
            height: heightCss,
        }">
        <div ref="pageHeader">
            <page-header>
                <template #title>{{ page.title ?? 'Page Builder'}}
                </template>
                <template #actions>
                    <div class="space-x-4">
                        <SecondaryButton @click="toggleSettings">Settings</SecondaryButton>

                        <PrimaryButton
                        @click="save"
                        :disabled="(!user().can('create-page') && !user().can('update-page')) || state.is(PageState.SUBMITTING)"
                    >
                            Save
                            <template
                                v-if="state.is(PageState.SUBMITTING)"
                                #icon
                            >
                                <LoadingIcon />
                            </template>
                        </PrimaryButton>
                    </div>
                </template>
            </page-header>
        </div>
    <div
        class="relative h-full w-full"

    >
        <Editor
            v-if="state.not(PageState.LOADING)"
            v-model:content="page.content"
            :blocks="blocks"
            :block-groups="blockGroups"
            :block-categories="blockCategories"
            :is-editor="true"
            @hide="showAside = false"
            :showAsideVar="!showAside"
            :hide-sidebar-blocks="!user().can('create-page')"
            :media-upload="mediaUpload"
        >
            <template #sidebar>
                <div class="col-span-1">
                    <Errors
                        :flush="false"
                        :error="error"
                    />
                    <Collapse
                            header-class="py-5 px-6"
                            :rounded="false"
                            chevron-colour="text-zinc-600"
                            ref="filter-collapse"
                        open
                    >
                        <template #title>Page Information</template>
                        <div
                            v-if="page"
                            key="group"
                            class="grid grid-cols-1 gap-3"
                        >
                            <SeInput
                                id="title"
                                v-model="page.title"
                                :error="error"
                                class="col-span-1"
                                type="text"
                                label="Title"
                                name="title"
                            />

                            <SeSelect
                                id="status"
                                v-model="page.status_id"
                                class="col-span-1"
                                label="Status"
                                field="id"
                                name="status"
                                :options="statuses"
                            />
                        </div>
                    </Collapse>
                </div>
            </template>
            <template #model_form>
                <Errors :error="error" class="mb-6" :dismissable="true" />
                <div class="p-6">
                    <FieldGroup v-model="page" :fields="fields" />
                </div>
            </template>
        </Editor>
        <CollapseTransition>
            <IndeterminateLoadingBar
                v-if="state.is(PageState.LOADING)"
                class="absolute left-0 right-0 z-10"
            />
        </CollapseTransition>
    </div>
</div>
</template>
<script>
import { Shark } from '@senses/builder';
import { markRaw } from 'vue';
import axios from 'axios';
import Pluralize from 'pluralize';
import { add, format } from 'date-fns';
import eventHub from '../../../Support/EventHub';
import PageHeader from '../../Ui/PageHeader.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import FieldGroup from '../../Ui/FieldGroup.vue';

import H1 from "../Builder/blocks/headers/H1.vue";
import H2 from "../Builder/blocks/headers/H2.vue";
import DarkButton from "../Builder/blocks/buttons/DarkButton.vue";
import LightButton from "../Builder/blocks/buttons/LightButton.vue";
import HeaderWithSubtitle from "../Builder/blocks/headers/HeaderWithSubtitle.vue";
import TwoEqualColumns from "../Builder/blocks/layouts/TwoEqualColumns.vue";
import ThreeEqualColumns from "../Builder/blocks/layouts/ThreeEqualColumns.vue";
import FourEqualColumns from "../Builder/blocks/layouts/FourEqualColumns.vue";
import TwoThirdsOneThird from "../Builder/blocks/layouts/TwoThirdsOneThird.vue";
import FullWidthImageHeader from "../Builder/blocks/headers/FullWidthImageHeader.vue";
import Paragraph from "../Builder/blocks/contents/Paragraph.vue";
import TextWithButton from "../Builder/blocks/contents/TextWithButton.vue";
import ImageAndText from "../Builder/blocks/contents/ImageAndText.vue";
import SingleImage from "../Builder/blocks/images/SingleImage.vue";
import VideoBlock from "../Builder/blocks/images/VideoBlock.vue";
import FullWidthImageBanner from "../Builder/blocks/images/FullWidthImageBanner.vue";
import FullWidthImageBannerWithColumns from "../Builder/blocks/images/FullWidthImageBannerWithColumns.vue";
import DynamicGrid from "../Builder/blocks/images/DynamicGrid.vue";
import FeaturesRepeaterGrid from "../Builder/blocks/repeaters/FeaturesRepeaterGrid.vue";
import FeatureWithHeadingImage from "../Builder/blocks/repeaters/FeatureWithHeadingImage.vue";
import RatingPictureRepeaterGrid from "../Builder/blocks/repeaters/RatingPictureRepeaterGrid.vue";
import ButtonsRepeaterGrid from "../Builder/blocks/repeaters/ButtonsRepeaterGrid.vue";
import BlogCardRepeater from "../Builder/blocks/repeaters/BlogCardRepeater.vue";
import FullWidthList from "../Builder/blocks/repeaters/FullWidthList.vue";

import Collapse from '../../Ui/Collapse/Collapse.vue';
import CollapseTransition from '../../Ui/Collapse/CollapseTransition.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import SeDatePicker from '../../Ui/Inputs/SeDatePicker.vue';
import Errors from '../../Ui/Errors/Errors.vue';
import dataBus from '../Builder/dataBus';
import PageState from '../../../States/PageState';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import { getBackendClientConfig } from '../../../Support/client';


import LayoutsCategoryIcon from "../builder/icons/svg/category-icons/layouts-category-icon.svg?raw";
import HeadingsCategoryIcon from "../builder/icons/svg/category-icons/headings-category-icon.svg?raw";
import ContentsCategoryIcon from "../builder/icons/svg/category-icons/contents-category-icon.svg?raw";
import ButtonsCategoryIcon from "../builder/icons/svg/category-icons/buttons-category-icon.svg?raw";
import ImagesCategoryIcon from "../builder/icons/svg/category-icons/images-category-icon.svg?raw";
import { LabelSelectMap } from '../../../Enums/CategoryType';

const { Editor } = Shark;

export default {
    components: {
        Editor,
        SeInput,
        SeSelect,
        SeSelectSearch,
        Collapse,
        CollapseTransition,
        SeDatePicker,
        IndeterminateLoadingBar,
        Errors,
        PageHeader,
        PrimaryButton,
        SecondaryButton,
        LoadingIcon,
        FieldGroup,
    },
    props: {

        id: {
            type: Number,
            default: null,
        },
    },
    data() {
        const config = getBackendClientConfig();
        return {
            config: config,
            user: window.user,
            blockCategories:[
            {
                    slug: "buttons",
                    icon: ButtonsCategoryIcon,
                },
                {
                    slug: "layouts",
                    icon: LayoutsCategoryIcon,
                },
                {
                    slug: "headings",
                    icon: HeadingsCategoryIcon,
                },
                {
                    slug: "contents",
                    icon: ContentsCategoryIcon,
                },
                {
                    slug: "images",
                    icon: ImagesCategoryIcon,
                },
            ],
            blocks: [
            H1,
            H2,
            DarkButton,
            LightButton,
            HeaderWithSubtitle,
            TwoEqualColumns,
            ThreeEqualColumns,
            FourEqualColumns,
            TwoThirdsOneThird,
            FullWidthImageHeader,
            Paragraph,
            TextWithButton,
            ImageAndText,
            SingleImage,
            VideoBlock,
            FullWidthImageBanner,
            FullWidthImageBannerWithColumns,
            DynamicGrid,
            FeaturesRepeaterGrid,
            FeatureWithHeadingImage,
            RatingPictureRepeaterGrid,
            ButtonsRepeaterGrid,
            BlogCardRepeater,
            FullWidthList,
            ].map((component) => markRaw(component)),
            blockGroups: [],
            PageState,
            state: new PageState(),
            quotable: {},
            error: {},
            statuses: [],
            depots: [],
            company: null,
            showAside:true,
            page: {
                content:[],
				title: null,
				slug: null,
				excerpt: null,
				type: "default",
				status_id: null,
				meta_title: null,
				meta_description: null,
				featured: false,
				show_last_updated: false,
            },
            heightCss: '',
        };
    },

    computed:{
        fields() {
            return [
                { key: "title", type: "text"},
                { key: "slug", type: "text"},
                { key: "excerpt", type: "textarea"},
                {
                    key: 'type',
                    type: 'select',
                    options: LabelSelectMap,
                    field: 'id',
                },
                { key: "status_id", type: "select-search", field: "id", url: "api/v2/statuses?format=select-search&filter[status-group-slug]=page"},
                { key: "meta_title", type: "text"},
                { key: "meta_description", type: "textarea"},
                { key: "featured", type: "toggle"},
                { key: "show_last_updated", type: "toggle"},
            ]
        }
    },

    mounted() {
        this.$nextTick(() => {
            const height = this.$refs.pageHeader.offsetHeight; //55 is the height of the search bar nav
            this.heightCss = `calc(100% - ${height}px)`;
        });
    },
    created() {
        this.loadBuilder();
        this.loadStatuses();
        eventHub.on('page-save', () => {

        });
    },
    methods: {
        contentUpdated(){},
        async loadBuilder() {
            this.state.set(PageState.LOADING);
            if (this.id) {
                this.page = (
                    await axios.get(`/api/v2/pages/${this.id}`)
                ).data; // todo this should use page cache?
            }

            this.blockGroups = (
                await axios.get(
                    '/api/v2/block-groups?filter[type-includes]=page&format=all'
                )
            ).data;

            this.state.set(PageState.IDLE);
        },

        loadStatuses() {
            axios
                .get(
                    '/api/v2/statuses?format=select-search&filter[status-group-slug]=page'
                )
                .then((response) => {
                    this.statuses = response.data;
                    if (!this.page.status_id) {
                        this.page.status_id = this.statuses.find(
                            (status) => status.slug === 'pending'
                        ).id;
                    }
                });
        },
        formError(error) {
            this.state.set(PageState.ERROR);
            eventHub.emit('page-save-error');
            console.log(error);
            this.error = error.response ? error.response.data : error;

            if(!this.showAside) {
                this.showAside = true;
            }
        },
        formSuccess(response) {
            this.state.set(PageState.IDLE);
            eventHub.emit('pages-updated');

            window.location.href = `/pages/${response.data.id}`;
        },

        toggleAside() {
            this.showAside = !this.showAside;
        },

        save() {
            this.error = null;
            this.state.set(PageState.SUBMITTING);
            eventHub.emit('page-saving');
            if (this.id) {
                axios
                    .patch(`/api/v2/pages/${this.id}`, this.page)
                    .then((response) => {
                        this.formSuccess(response);
                    })
                    .catch((response) => {
                        this.formError(response);
                    });
            } else {
                axios
                    .post('/api/v2/pages', this.page)
                    .then((response) => {
                        this.formSuccess(response);
                    })
                    .catch((response) => {
                        this.formError(response);
                    });
            }
        },

        toggleSettings() {
            this.showAside = !this.showAside;
        },

        mediaUpload(file) {
            const formData = new FormData();
            formData.append('file', file.file);
            formData.append('public', 1);

            return axios.post('/api/v2/files', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
        }
    },
};
</script>
