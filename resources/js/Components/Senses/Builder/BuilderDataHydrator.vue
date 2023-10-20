<template>
    <div v-if="state.not(PageState.LOADING)">
        <slot :data="data"></slot>
    </div>
    <div v-else>
        <IndeterminateLoadingBar></IndeterminateLoadingBar>
    </div>
</template>

<script>
import axios from 'axios';
import PageState from '../../../States/PageState';
import IndeterminateLoadingBar from '../../Ui/IndeterminateLoadingBar.vue';

export default {
    components: {
        IndeterminateLoadingBar,
    },

    props: {
        model: {
            type: String,
            required: true,
        },
        id: {
            type: Number,
            required: true,
        },
        requestData: {
            type: Object,
            default: null,
        },
        filters: {
            type: Object,
            default: null,
        },
    },

    data: () => ({
        PageState,
        state: new PageState(),
        data: {},
        error: null,
    }),

    emits: ['error', 'setState'],

    mounted() {
        if (Object.values(this.filters).length == 0) {
            this.state.set(PageState.IDLE);
            this.$emit('setState', PageState.IDLE);
        }
    },

    methods: {
        load() {
            this.state.set(PageState.LOADING);
            this.error = null;
            if (this.id && this.model) {
                this.$emit('setState', PageState.LOADING);
                axios
                    .post(
                        '/api/v2/builder-data/' + this.model + '/' + this.id,
                        { criteria: this.requestData }
                    )
                    .then((response) => {
                        this.state.set(PageState.IDLE);
                        this.$emit('setState', PageState.IDLE);
                        this.data = response.data.data;
                        this.$emit('error', null);
                    })
                    .catch((error) => {
                        this.state.set(PageState.ERROR);
                        this.$emit('setState', PageState.ERROR);
                        this.error = error.response.data;
                        this.$emit('error', this.error);
                        this.$notifications.push({
                            title: 'Error',
                            description:
                                'There was a problem running this report.',
                            type: 'danger',
                        });
                    });
            } else {
                this.state.set(PageState.IDLE);
                this.$emit('setState', PageState.IDLE);
            }
        },
    },

    watch: {
        requestData: {
            deep: true,
            handler() {
                this.load();
            },
        },
    },
};
</script>
