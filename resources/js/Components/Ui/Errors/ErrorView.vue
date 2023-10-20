<template>
    <AsideLayout flush v-bind="$props" :loading="state.is(AsideState.LOADING)" size="lg">
        <template #title>Error</template>

        <div class="space-y-1 p-4">
            {
            <div v-for="(value, key) in error" class="ml-8">
                <div v-if="typeof value == 'object'" class="space-y-1">
                    <span>"{{ key }}": {</span>
                    <div v-for="(subValue, subKey) in value" class="ml-8">
                        <span>"{{ subKey }}":</span> <span class="text-zinc-500">{{ subValue ?? 'null' }}</span>
                    </div>
                    }
                </div>
                <div v-else>
                    <span>"{{ key }}":</span> <span class="text-zinc-500">{{ value ?? 'null' }}</span>
                </div>
            </div>
            }
        </div>
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import Aside from '../../../Mixins/Aside';
import axios from 'axios';
import AsideState from "../../../States/AsideState";
import titleCase from '../../../Filters/TitleCase.js';


export default {
    components: {
        AsideLayout,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,
            user: window.user,
            error: null,
        };
    },

    mounted() {
        this.load();
    },

    computed: {

    },

    methods: {
        titleCase,
        load() {
            this.state.set(AsideState.LOADING);
            axios
                .get('/api/v2/errors/' + this.data.error.uuid)
                .then((response) => {
                    this.error = response.data;
                    this.state.set(AsideState.IDLE);
                })
                .catch((error) => {
                    this.error = error;
                    this.state.set(AsideState.ERROR);
                });
        },
    },
};
</script>
