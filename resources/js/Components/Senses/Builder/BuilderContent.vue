<template>
    <BuilderCriteria class="z-30" :model="model" v-show="showFilters" v-if="model?.filters && Object.keys(model.filters).length" :filters="model?.filters" @submitCriteria="runReport" :error="error" />
    <div class="px-4 py-2">
        <BuilderDataHydrator v-slot="{ data }" model="report-layout" :id="model.id" :request-data="requestData" @error="setError" :filters="model.filters" @set-state="setState">
            <Renderer v-if="state.is(PageState.IDLE)" :content="model.content" :blocks="blocks" :data="data" :request-data="requestData" />
            <Errors v-if="error && Object.keys(error).length" :error="error" />
        </BuilderDataHydrator>
    </div>
</template>
<script>
import Renderer from '../Builder/Renderer.vue';
import BuilderDataHydrator from '../Builder/BuilderDataHydrator.vue';
import BuilderCriteria from '../Builder/BuilderCriteria.vue';
import Errors from '../../Ui/Errors/Errors.vue';
import reportLayoutBlocks from '../ReportLayouts/reportLayoutBlocks.js';

import { markRaw } from 'vue';

import PageState from '../../../States/PageState';

export default {
    components: {
        Renderer,
        BuilderCriteria,
        Errors,
        BuilderDataHydrator,
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        showFilters: {
            type: Boolean,
            default: true,
        },
        defaultCriteria: {
            type: Object,
            required: false,
        },
    },
    data() {
        return {
            blocks: Object.values(reportLayoutBlocks).map((component) => markRaw(component)),
            criteria: [],
            error: null,
            state: new PageState(),
            PageState,
        };
    },
    mounted() {
    },

    methods: {
        runReport(criteria) {
            this.criteria = { ...criteria };
        },
        setError(error) {
            this.error = error;
        },
        setState(state){
            this.state.set(state);
        }
    },

    computed: {
        requestData() {
            return this.criteria && Object.keys(this.criteria).length ? this.criteria : null;
        },
    },

    watch: {
        'defaultCriteria.date': function(newVal){
            newVal ? this.criteria['date'] = newVal : null;
        },
        'defaultCriteria.date_range': function(newVal){
            newVal ? this.criteria['date_range'] = newVal : null;
        }
    }
};
</script>
