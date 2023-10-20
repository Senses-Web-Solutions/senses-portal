<template>
    <Editor
        v-if="!state.is(PageState.LOADING)"
        v-model:content="blockGroup.content"
        :blocks="blocks"
        :block-groups="blockGroups"
        :is-editor="true"
    >
        <template #sidebar>
            <div class="col-span-1 border-r border-zinc-200 bg-white">
                <Errors
                    :flush="false"
                    :error="error"
                />
                <Collapse
                    header-class="border-t border-zinc-200 py-5 px-6"
                    :rounded="false"
                    chevron-colour="text-zinc-600"
                    ref="filter-collapse"
                    open
                >
                    <template #title>Block Group Information</template>
                    <div class="space-y-2">
                        <SeInput
                            id="display_name"
                            v-model="blockGroup.display_name"
                            class="col-span-1"
                            type="text"
                            label="Title"
                            name="display_name"
                        />
                        <SeSelect
                            id="block_group_types"
                            v-model="blockGroup.block_group_types"
                            :text-field="null"
                            class="col-span-1"
                            :options="blockGroupTypes"
                            label="Type"
                            :multiple="true"
                            :track-by="null"
                            name="block_group_types"
                        />
                        <SeTextArea
                            id="description"
                            v-model="blockGroup.description"
                            label="Description"
                            name="description"
                        />
                    </div>
                </Collapse>
            </div>
        </template>
    </Editor>
    <CollapseTransition>
        <IndeterminateLoadingBar
            v-if="state.is(PageState.LOADING)"
            class="absolute left-0 right-0 z-10"
        />
    </CollapseTransition>
</template>

<script>
import { Shark, Core } from '@senses/builder';
import { markRaw } from 'vue';
import axios from 'axios';
import SeTextArea from '../../Ui/Inputs/SeTextArea.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import SeSelectBasic from '../../Ui/Inputs/SeSelectBasic.vue';
import eventHub from '../../../Support/EventHub';
import H1 from '../Builder/blocks/H1.vue';
import H2 from '../Builder/blocks/H2.vue';
import H3 from '../Builder/blocks/H3.vue';
import H4 from '../Builder/blocks/H4.vue';
import H5 from '../Builder/blocks/H5.vue';
import H6 from '../Builder/blocks/H6.vue';
import Paragraph from '../Builder/blocks/Paragraph.vue';
import TwoEqualColumns from '../Builder/blocks/layouts/TwoEqualColumns.vue';
import ThreeEqualColumns from '../Builder/blocks/layouts/ThreeEqualColumns.vue';
import FourEqualColumns from '../Builder/blocks/layouts/FourEqualColumns.vue';
import OneThirdTwoThirdsColumns from '../Builder/blocks/Layouts/OneThirdTwoThirdsColumns.vue';
import TwoThirdsOneThirdColumns from '../Builder/blocks/Layouts/TwoThirdsOneThirdColumns.vue';
import OneQuarterThreeQuartersColumns from '../Builder/blocks/Layouts/OneQuarterThreeQuartersColumns.vue';
import ThreeQuartersOneQuarterColumns from '../Builder/blocks/Layouts/ThreeQuartersOneQuarterColumns.vue';

import QuoteLineTable from '../Builder/blocks/Quotes/QuoteLineTable.vue';
import QuoteLineTotal from '../Builder/blocks/Quotes/QuoteLineTotal.vue';
import QuoteLineGroupTotal from '../Builder/blocks/Quotes/QuoteLineGroupTotal.vue';
import QuoteLineRunningTotal from '../Builder/blocks/Quotes/QuoteLineRunningTotal.vue';
import QuoteSystemAddress from '../Builder/blocks/Quotes/QuoteSystemAddress.vue';
import QuoteClientAddress from '../Builder/blocks/Quotes/QuoteClientAddress.vue';

import Collapse from '../../Ui/Collapse/Collapse.vue';
import CollapseTransition from '../../Ui/Collapse/CollapseTransition.vue';
import PageState from '../../../States/PageState';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';
import Errors from '../../Ui/Errors/Errors.vue';

const { Editor } = Shark;

export default {
    components: {
        Editor,
        SeInput,
        Errors,
        SeTextArea,
        SeSelect,
        Collapse,
        CollapseTransition,
        IndeterminateLoadingBar,
    },
    props: {
        id: {
            type: [Number, null],
            required: false,
            default: null,
        },
    },
    data() {
        return {
            blocks: [
                Paragraph,
                H1,
                H2,
                H3,
                H4,
                H5,
                H6,
                TwoEqualColumns,
                ThreeEqualColumns,
                FourEqualColumns,
                TwoThirdsOneThirdColumns,
                OneThirdTwoThirdsColumns,
                OneQuarterThreeQuartersColumns,
                ThreeQuartersOneQuarterColumns,
                QuoteLineTable,
                QuoteLineTotal,
                QuoteLineGroupTotal,
                QuoteLineRunningTotal,
                QuoteSystemAddress,
                QuoteClientAddress,
            ].map((component) => markRaw(component)),
            blockGroup: {
                display_name: null,
                description: null,
                content: [],
                block_group_types: [],
            },
            error: {},
            PageState,
            state: new PageState(),
            blockGroupTypes: ['quote', 'report_layout'],
            blockGroups: [],
        };
    },
    created() {
        this.loadBuilder();

        eventHub.on('block-group-save', () => {
            const { blockGroup } = this;
            if (this.id) {
                axios
                    .patch('/api/v2/block-groups/' + this.id, blockGroup)
                    .then((response) => {
                        this.formSuccess(response);
                    })
                    .catch((response) => {
                        this.formError(response);
                    });
            } else {
                axios
                    .post('/api/v2/block-groups', blockGroup)
                    .then((response) => {
                        this.formSuccess(response);
                    })
                    .catch((response) => {
                        this.formError(response);
                    });
            }
        });
    },
    methods: {
        async loadBuilder() {
            if (this.id) {
                this.blockGroup = (
                    await axios.get(`/api/v2/block-groups/${this.id}`)
                ).data; // todo this should use block group cache?
            }
            this.state.set(PageState.IDLE);
        },
        formError(error) {
            this.state.set(PageState.ERROR);
            this.error = error.response ? error.response.data : error;
        },
        formSuccess(response) {
            this.state.set(PageState.IDLE);
            window.location.href = '/block-groups/' + response.data.id;
        },
    },
};
</script>
