<template>
    <SensesTable
        url="/api/v2/status-groups"
        table="status_groups"
        setting="table-status-groups"
        event="status-group-updated"
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

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },

				{ label: "Title", key: "title" },
				{ label: "Slug", key: "slug" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-status-group')) {
                if (type && type === 'blank') {
                    window.open("/status-groups/" + row.id);
                } else {
                    window.location.href = "/status-groups/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 09-10-2023 12:05:02
