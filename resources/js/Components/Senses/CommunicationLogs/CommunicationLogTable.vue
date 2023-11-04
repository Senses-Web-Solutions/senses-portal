<template>
    <SensesTable
        url="/api/v2/communication-logs"
        table="communication_logs"
        setting="table-communication-logs"
        event="communication-log-updated"
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
                    
				{ label: "Type", key: "type" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-communication-log')) {
                if (type && type === 'blank') {
                    window.open("/communication-logs/" + row.id);
                } else {
                    window.location.href = "/communication-logs/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:50
