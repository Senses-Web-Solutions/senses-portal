<template>
    <SensesTable
        url="/api/v2/tag-groups"
        table="tag_groups"
        setting="table-tag-groups"
        event="tag-group-updated"
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
            if (row.id && user().can('show-tag-group')) {
                if (type && type === 'blank') {
                    window.open("/tag-groups/" + row.id);
                } else {
                    window.location.href = "/tag-groups/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 09-10-2023 10:26:55
