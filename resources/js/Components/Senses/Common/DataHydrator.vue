<template>
    <!-- Data Hydrator -->
    <div v-if="!loading && data">
        <slot :data="data" :loading="loading"></slot>
    </div>
    <div v-else>
        <IndeterminateLoadingBar />
        <slot name="skeleton"></slot>
    </div>
</template>
<script>
import axios from 'axios';
import EventHub from '../../../Support/EventHub';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';

export default {
    components: {
        IndeterminateLoadingBar,
    },
    props: {
        url: String,
        events: {
            type: Array,
            default () {
                return [];
            },
        },
        model: {
            type: String,
            default: null
        },
        maintainer: {
            type: String,
            default: 'Maintainer',
        },
        hasFiles: {
            type: Boolean,
            default: false,
        },
        hasComments: {
            type: Boolean,
            default: false,
        },
    },
    data: () => ({
        data: {},
        loading: true,
        dataMaintainer: null,
    }),
    created () {
        this.load();
        this.invokeEventListeners();
    },
    methods: {
        async load () {
            this.loading = true;
            if (this.url) {
                const response = await axios.get(this.url);
                this.data = response.data;
                await this.importMaintainer();
            }
            this.loading = false;
        },
        invokeEventListeners () {
            this.events.forEach((event) => {
                EventHub.on(event, (data = null) => {
                    if (data == null) {
                        this.load();
                    } else {
                        this.data = { ...this.data, ...data };
                    }
                });
            });
        },
        async importMaintainer () {
            if (this.maintainer) {
                // new myMaintainer(data);
                const Maintainer = (
                    await import(
                        `../../../Support/Maintainers/${this.maintainer}.js`
                    )
                ).default;

                if (this.maintainer !== 'Maintainer') {
                    this.dataMaintainer = new Maintainer(this.data);
                } else if (this.model) {
                    this.dataMaintainer = new Maintainer(this.model, this.data, this.hasFiles, this.hasComments);
                }
            }
        },
        unmounted () {
            this.dataMaintainer.destroy();
        },
    },
};
</script>
