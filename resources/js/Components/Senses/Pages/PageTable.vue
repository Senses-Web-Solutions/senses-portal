<template>
    <SensesTable
        :url="url"
        table="pages"
        setting="table-pages"
        event="page-updated"
        :fields="fields"
        :actions="actions"
        @row-click="rowClick"
    />
</template>

<script>

import SensesTable from '../Tables/SensesTable.vue';

export default {
    components: {
        SensesTable,
    },

    props: {
        url: {
            type: String,
            default: '/api/v2/pages'
        }
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },

				{ label: "Title", key: "title" },
				{ label: "Slug", key: "slug" },
				{ label: "Featured", key: "featured" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-page')) {
                if (type && type === 'blank') {
                    window.open("/pages/" + row.id);
                } else {
                    window.location.href = "/pages/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 10-10-2023 14:43:35
