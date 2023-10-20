<template>
    <AsideLayout
        size="sm"
        v-bind="$props"
        :loading="state.is(AsideState.LOADING)"
        flush
    >
        <template #title>
            <span class="capitalize"
                >{{ data.revisionable_type }}
                {{ data.revisionable_id }} History</span
            >
        </template>

        <div v-if="state.not(AsideState.LOADING) && revisions">
            <div
                v-for="revision in revisions"
                class="space-y-2 px-4 py-2"
            >
                <div class="text-lg text-primary-700">
                    {{
                        format(
                            new Date(revision.created_at),
                            'EEEE do MMMM yyyy - hh:mmaaa'
                        )
                    }}
                </div>
                <BasicTable
                    as="div"
                    bordered
                    :selectable="false"
                    :columns="columns"
                    :rows="Object.values(revision.fields)"
                />
            </div>
        </div>
    </AsideLayout>
</template>

<script>
import { format } from 'date-fns';
import pluralize from 'pluralize';
import AsideLayout from '../../Layout/AsideLayout.vue';
import BasicTable from '../Tables/BasicTable.vue';
import Aside from '../../../Mixins/Aside';
import axios from 'axios';
import eventHub from '../../../Support/EventHub';
import AsideState from '../../../States/AsideState';

export default {
    components: {
        BasicTable,
        AsideLayout,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,
            revisions: [],
            columns: [
                { id: 'field', title: 'Field' },
                { id: 'before', title: 'Before' },
                { id: 'after', title: 'After' },
            ],
        };
    },

    mounted() {
        this.load();
        eventHub.on('rate-updated', () => {
            this.$asides.pop();
        });
    },

    methods: {
        format,
        load() {
            this.state.set(AsideState.LOADING);
            axios
                .get(
                    '/api/v2/' +
                        pluralize(this.data.revisionable_type) +
                        '/' +
                        this.data.revisionable_id +
                        '/history'
                )
                .then((response) => {
                    this.revisions = response.data;
                    this.mapRevisions();
                });
        },
        mapRevisions() {
            this.revisions = this.revisions
                .filter(
                    (revision) =>
                        Object.keys(revision.before).length > 0 &&
                        Object.keys(revision.after).length > 0
                )
                .map((revision) => {
                    var fields = [];
                    Object.keys(revision.before).forEach((field) => {
                        fields[field] = {
                            field: field,
                            before: revision.before[field] ?? null,
                            after: revision.after[field] ?? null,
                        };
                    });
                    return {
                        created_at: revision.created_at,
                        fields: fields,
                    };
                });

            this.state.set(AsideState.IDLE);
        },
    },
};
</script>
