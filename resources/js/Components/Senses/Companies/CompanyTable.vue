<template>
    <SensesTable
        url="/api/v2/companies"
        table="companies"
        setting="table-companies"
        event="company-updated"
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
            if (row.id && user().can('show-company')) {
                if (type && type === 'blank') {
                    window.open("/companies/" + row.id);
                } else {
                    window.location.href = "/companies/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:45
